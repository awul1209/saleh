from flask import Flask, render_template, request, jsonify
from flask_cors import CORS
import pymysql.cursors
import json

app = Flask(__name__)
# PENTING: Mengaktifkan CORS agar bisa diakses dari server PHP Anda
CORS(app)

# --- Konfigurasi Database ---
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'lowong',
    'cursorclass': pymysql.cursors.DictCursor
}

def get_db_connection():
    return pymysql.connect(**DB_CONFIG)

# --- FUNGSI-FUNGSI UTAMA ---

def load_profil_dan_bobot_by_bidang(bidang_id):
    conn = None
    try:
        conn = get_db_connection()
        with conn.cursor() as cursor:
            # Load Profil Ideal dengan normalisasi
            cursor.execute("SELECT kriteria, nilai FROM profil_ideal WHERE bidang_id = %s", (bidang_id,))
            profil_ideal = {row['kriteria'].lower().strip().replace(' ', '_'): int(row['nilai']) for row in cursor.fetchall()}
            
            # Load Bobot Kriteria dengan normalisasi
            cursor.execute("SELECT kriteria, bobot FROM bobot_kriteria WHERE bidang_id = %s", (bidang_id,))
            bobot_kriteria = {row['kriteria'].lower().strip().replace(' ', '_'): float(row['bobot']) for row in cursor.fetchall()}
            
            return profil_ideal, bobot_kriteria
    except Exception as e:
        print(f"Error saat memuat profil dan bobot: {e}")
        return {}, {}
    finally:
        if conn:
            conn.close()

def konversi_gap_ke_skor(gap):
    gap_map = { 0: 5.0, 1: 4.5, -1: 4.0, 2: 3.5, -2: 3.0, 3: 2.5, -3: 2.0, 4: 1.5, -4: 1.0 }
    return gap_map.get(gap, 1.0)

def calculate_single_candidate_score(candidate_data, profil_ideal, bobot_kriteria):
    total_weighted_score = 0
    total_bobot = 0
    penjelasan_skor = []
    
    for kriteria, ideal_value in profil_ideal.items():
        if kriteria in candidate_data and kriteria in bobot_kriteria:
            kandidat_value = candidate_data[kriteria]
            bobot = bobot_kriteria[kriteria]
            gap = kandidat_value - ideal_value
            skor_gap = konversi_gap_ke_skor(gap)
            weighted_score = skor_gap * bobot
            total_weighted_score += weighted_score
            total_bobot += bobot
            penjelasan_skor.append(f"{kriteria.replace('_', ' ').title()}: GAP {gap}, Skor Konversi {skor_gap:.2f}, Bobot {bobot:.2f}, Skor Terbobot {weighted_score:.2f}")
    
    max_possible_score = 5.0 * total_bobot if total_bobot > 0 else 0
    if max_possible_score == 0: return 0.00, penjelasan_skor
    final_score = (total_weighted_score / max_possible_score) * 100
    return round(final_score, 2), penjelasan_skor

# --- RUTE-RUTE APLIKASI ---

@app.route('/')
def index():
    return render_template('index.php')

@app.route('/get_parent_bidang', methods=['GET'])
def get_parent_bidang():
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            cursor.execute("SELECT id, nama_bidang FROM bidang WHERE parent_id = 0 ORDER BY nama_bidang")
            return jsonify(cursor.fetchall())
    finally:
        conn.close()

@app.route('/get_child_bidang/<int:parent_id>', methods=['GET'])
def get_child_bidang(parent_id):
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            cursor.execute("SELECT id, nama_bidang FROM bidang WHERE parent_id = %s ORDER BY nama_bidang", (parent_id,))
            return jsonify(cursor.fetchall())
    finally:
        conn.close()

@app.route('/get_profil_bobot_by_bidang/<int:bidang_id>', methods=['GET'])
def get_profil_bobot_by_bidang_route(bidang_id):
    profil_ideal, bobot_kriteria = load_profil_dan_bobot_by_bidang(bidang_id)
    if not profil_ideal or not bobot_kriteria:
        return jsonify({"error": "Data profil ideal atau bobot tidak ditemukan untuk bidang ini."}), 404
    return jsonify({'profil_ideal': profil_ideal, 'bobot_kriteria': bobot_kriteria})

@app.route('/get_unique_years', methods=['GET'])
def get_unique_years():
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            cursor.execute("SELECT DISTINCT tahun_daftar FROM calon_karyawan ORDER BY tahun_daftar DESC")
            years = [row['tahun_daftar'] for row in cursor.fetchall()]
            return jsonify(years)
    finally:
        conn.close()

@app.route('/get_candidates', methods=['GET'])
def get_candidates():
    tahun = request.args.get('tahun')
    parent_id = request.args.get('parent_id')
    bidang_id = request.args.get('bidang_id')
    
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            query = "SELECT id, nama, tahun_daftar FROM calon_karyawan"
            params = []
            where_clauses = []
            if tahun and tahun.isdigit():
                where_clauses.append("tahun_daftar = %s")
                params.append(int(tahun))
            if bidang_id and bidang_id.isdigit():
                where_clauses.append("bidang_id = %s")
                params.append(int(bidang_id))
            elif parent_id and parent_id.isdigit():
                where_clauses.append("bidang_id IN (SELECT id FROM bidang WHERE parent_id = %s)")
                params.append(int(parent_id))
            if where_clauses:
                query += " WHERE " + " AND ".join(where_clauses)
            query += " ORDER BY nama ASC"
            cursor.execute(query, tuple(params))
            return jsonify(cursor.fetchall())
    except Exception as e:
        print(f"Error fetching candidates: {e}")
        return jsonify({"error": "Gagal mengambil data calon karyawan."}), 500
    finally:
        conn.close()

@app.route('/match_selected_candidates', methods=['POST'])
def match_selected_candidates():
    data = request.json
    selected_ids = data.get('candidate_ids', [])
    bidang_id = data.get('bidang_id')

    if not bidang_id: return jsonify({"error": "Bidang belum dipilih."}), 400
    profil_ideal_aktif, bobot_kriteria_aktif = load_profil_dan_bobot_by_bidang(bidang_id)
    if not profil_ideal_aktif: return jsonify({"error": "Profil ideal tidak ditemukan."}), 404
    if not selected_ids: return jsonify({"message": "Tidak ada kandidat yang dipilih."}), 200

    conn = get_db_connection()
    try:
        results = []
        with conn.cursor() as cursor:
            for candidate_id in selected_ids:
                cursor.execute("SELECT * FROM calon_karyawan WHERE id = %s", (candidate_id,))
                candidate_details = cursor.fetchone()
                if not candidate_details: continue

                cursor.execute("SELECT kriteria, nilai FROM nilai_kandidat WHERE calon_karyawan_id = %s", (candidate_id,))
                nilai_rows = cursor.fetchall()
                candidate_criteria = {row['kriteria']: row['nilai'] for row in nilai_rows}
                
                skor_kecocokan, penjelasan = calculate_single_candidate_score(candidate_criteria, profil_ideal_aktif, bobot_kriteria_aktif)
                
                results.append({
                    'id': candidate_id, 'nama': candidate_details['nama'],
                    'skor_kecocokan': skor_kecocokan, 'detail_kandidat': candidate_criteria,
                    'penjelasan_skor': penjelasan, 'bidang_id': int(bidang_id)
                })
        
        results.sort(key=lambda x: x['skor_kecocokan'], reverse=True)
        return jsonify(results)
    except Exception as e:
        print(f"Error saat menghitung kecocokan: {e}")
        return jsonify({"error": f"Gagal menghitung kecocokan: {e}"}), 500
    finally:
        conn.close()

@app.route('/save_matching_results', methods=['POST'])
def save_matching_results():
    data = request.json
    if not data: return jsonify({"error": "Tidak ada data yang disediakan."}), 400
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            for result in data:
                calon_karyawan_id = result.get('id'); bidang_id = result.get('bidang_id')
                skor_kecocokan = result.get('skor_kecocokan'); penjelasan_skor_json = json.dumps(result.get('penjelasan_skor', []))
                if not all([calon_karyawan_id, bidang_id, skor_kecocokan is not None]): continue
                cursor.execute(
                    "INSERT INTO hasil_pencocokan (calon_karyawan_id, bidang_id, skor_kecocokan, detail_penjelasan_skor) VALUES (%s, %s, %s, %s)",
                    (calon_karyawan_id, bidang_id, skor_kecocokan, penjelasan_skor_json)
                )
            conn.commit()
        return jsonify({"message": "Hasil pencocokan berhasil disimpan!"}), 200
    except Exception as e:
        print(f"Error saat menyimpan hasil: {e}")
        conn.rollback()
        return jsonify({"error": f"Gagal menyimpan hasil: {e}"}), 500
    finally:
        conn.close()

if __name__ == '__main__':
    app.run(debug=True)
