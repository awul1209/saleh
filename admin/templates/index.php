<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perhitungan Profile Matching</title>
    
    <!-- Memuat Font dari Google Fonts dan Ikon dari Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        /* CSS untuk Tampilan Terpusat dan Lebih Elegan */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --body-bg: #f0f2f5;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            padding: 40px 15px;
        }
        .main-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 950px;
            margin: auto;
        }
        .header-section h1 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .header-section p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 30px auto;
        }
        .panel {
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }
        .panel-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        #candidatesSelection {
            margin-top: 25px;
        }
        .candidate-list-box {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            padding: 10px;
        }
        .candidate-item {
            display: flex;
            align-items: center;
            padding: 12px 8px;
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s;
        }
        .candidate-item:last-child { border-bottom: none; }
        .candidate-item:hover { background-color: #f8f9fa; }
        .candidate-item label { flex-grow: 1; cursor: pointer; margin-bottom: 0; }
        .candidate-item .tahun { color: var(--text-muted); font-size: 0.9em; }
        .btn-action {
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
        }
        .candidate-card {
            background-color: var(--light-bg);
            border: 1px solid var(--border-color);
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .candidate-card:hover {
             transform: translateY(-5px);
             box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .rank-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        .rank-1 { background-color: #ffc107; }
        .rank-2 { background-color: #adb5bd; }
        .rank-3 { background-color: #cd7f32; }
        .rank-other { background-color: var(--secondary-color); }
        .card-header-result { display: flex; align-items: center; }
        .candidate-name { font-size: 1.4rem; font-weight: 600; color: var(--text-dark); }
        .candidate-score { font-size: 1.8rem; font-weight: 700; color: var(--success-color); margin-left: auto; }
        .card-body-result { padding-top: 15px; border-top: 1px solid #eee; margin-top: 15px;}
        .card-body-result strong { color: var(--text-dark); }
        .card-body-result ul { padding-left: 20px; font-size: 0.95em; }
        .info-box {
            display: none;
            background-color: #e9f5ff;
            border: 1px solid #b6daff;
            border-radius: 8px;
            margin-top: 20px;
            padding: 15px;
        }
        .info-box strong { color: #0056b3; }
        .loading-spinner { display: none; border: 4px solid #f3f3f3; border-top: 4px solid #0d6efd; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-section text-center">
            <h1><i class="fas fa-bullseye text-primary"></i> Sistem Perhitungan Profile Matching</h1>
            <p>Analisis dan temukan kandidat terbaik yang sesuai dengan profil ideal perusahaan.</p>
        </div>

        <div class="panel">
            <h2 class="panel-title"><i class="fas fa-filter"></i> Panel Seleksi & Kriteria</h2>
            <div class="row g-3">
                <div class="col-md-4 filter-group">
                    <label for="parentBidangFilter" class="form-label">1. Kategori Bidang</label>
                    <select id="parentBidangFilter" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                    </select>
                </div>
                <div class="col-md-4 filter-group">
                    <label for="childBidangFilter" class="form-label">2. Sub-Bidang (Posisi)</label>
                    <select id="childBidangFilter" class="form-select" disabled>
                        <option value="">-- Semua Sub-Bidang --</option>
                    </select>
                </div>
                <div class="col-md-4 filter-group">
                    <label for="yearFilter" class="form-label">3. Periode Tahun</label>
                    <select id="yearFilter" class="form-select">
                        <option value="">Semua Tahun</option>
                    </select>
                </div>
            </div>
             <div class="info-box" id="infoBox">
                <div class="row">
                    <div class="col-md-6"><strong>Profil Ideal:</strong><ul id="profilIdealList" class="list-unstyled mt-1"></ul></div>
                    <div class="col-md-6"><strong>Bobot Kriteria:</strong><ul id="bobotKriteriaList" class="list-unstyled mt-1"></ul></div>
                </div>
            </div>
            
            <div id="candidatesSelection">
                <h4 class="mt-4">4. Pilih Calon Karyawan</h4>
                <div class="select-all-container">
                    <input type="checkbox" id="selectAllCandidates" class="form-check-input" disabled>
                    <label for="selectAllCandidates" class="form-check-label ms-2">Pilih Semua Kandidat</label>
                </div>
                <div class="candidate-list-box mt-2">
                    <div id="candidatesCheckboxes"><p id="selectBidangMessage" class="text-muted p-3 text-center">Pilih kategori atau sub-bidang untuk menampilkan daftar calon.</p></div>
                    <p id="noCandidatesMessage" style="display:none;" class="text-muted p-3 text-center">Tidak ada calon karyawan yang ditemukan.</p>
                </div>
            </div>

            <div class="row mt-4 g-2">
                <div class="col-md-6"><button id="matchSelectedButton" class="btn btn-primary btn-action" disabled><i class="fas fa-calculator"></i> Hitung Skor</button></div>
                <div class="col-md-6"><a href="http://localhost/profil/admin/" class="btn btn-secondary btn-action"><i class="fas fa-arrow-left"></i> Kembali ke Admin</a></div>
            </div>
        </div>
        
        <div class="panel" id="resultsPanel" style="display:none;">
             <h2 class="panel-title"><i class="fas fa-chart-bar"></i> Hasil Peringkat</h2>
             <div class="loading-spinner" id="loadingSpinner"></div>
             <div id="candidatesList"></div>
             <button id="saveResultsButton" class="btn btn-success w-100 mt-3" style="display:none;"><i class="fas fa-save"></i> Simpan Semua Hasil</button>
             <p id="saveMessage" class="mt-2 text-center"></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        const BASE_URL = 'http://127.0.0.1:5000';
        let currentActiveParentId = null;
        let currentActiveChildId = null;
        let lastMatchingResults = [];
        let activeCriteriaKeys = []; 

        const parentBidangFilter = $('#parentBidangFilter');
        const childBidangFilter = $('#childBidangFilter');
        const yearFilter = $('#yearFilter'); 
        const selectAllCheckbox = $('#selectAllCandidates');
        const candidatesCheckboxesDiv = $('#candidatesCheckboxes');
        const noCandidatesMessage = $('#noCandidatesMessage');
        const selectBidangMessage = $('#selectBidangMessage');
        const matchSelectedButton = $('#matchSelectedButton');
        const loadingSpinner = $('#loadingSpinner');
        const resultsPanel = $('#resultsPanel');
        const candidatesListDiv = $('#candidatesList');
        const profilIdealList = $('#profilIdealList');
        const bobotKriteriaList = $('#bobotKriteriaList');
        const infoBox = $('#infoBox');
        const saveResultsButton = $('#saveResultsButton');
        const saveMessage = $('#saveMessage');

        function toTitleCase(str) {
            return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        async function loadParentBidang() {
            try {
                const response = await fetch(`${BASE_URL}/get_parent_bidang`);
                const bidangList = await response.json();
                if (response.ok) {
                    bidangList.forEach(bidang => {
                        parentBidangFilter.append(new Option(bidang.nama_bidang, bidang.id));
                    });
                }
            } catch (error) { console.error('Error loading parent bidang:', error); }
        }

        async function loadChildBidang(parentId) {
            childBidangFilter.html('<option value="">-- Semua Sub-Bidang --</option>');
            if (!parentId) {
                childBidangFilter.prop('disabled', true);
                return;
            }
            childBidangFilter.prop('disabled', false);
            try {
                const response = await fetch(`${BASE_URL}/get_child_bidang/${parentId}`);
                const bidangList = await response.json();
                if (response.ok) {
                    bidangList.forEach(bidang => {
                        childBidangFilter.append(new Option(bidang.nama_bidang, bidang.id));
                    });
                }
            } catch (error) { console.error('Error loading child bidang:', error); }
        }
        
        async function loadProfilAndBobot(bidangId) {
            infoBox.show();
            profilIdealList.html('<li>Memuat...</li>');
            bobotKriteriaList.html('<li>Memuat...</li>');
            try {
                const response = await fetch(`${BASE_URL}/get_profil_bobot_by_bidang/${bidangId}`);
                const data = await response.json();
                if (response.ok) {
                    profilIdealList.empty();
                    bobotKriteriaList.empty();
                    activeCriteriaKeys = Object.keys(data.profil_ideal); 
                    for (const kriteria in data.profil_ideal) {
                        profilIdealList.append(`<li>${toTitleCase(kriteria)}: <strong>${data.profil_ideal[kriteria]}</strong></li>`);
                    }
                    for (const kriteria in data.bobot_kriteria) {
                        bobotKriteriaList.append(`<li>${toTitleCase(kriteria)}: <strong>${data.bobot_kriteria[kriteria]}</strong></li>`);
                    }
                }
            } catch (error) { console.error('Error loading profil/bobot:', error); }
        }

        async function loadCandidates(parentId = '', childId = '', year = '') {
            candidatesCheckboxesDiv.html('<p class="text-muted text-center p-3">Memuat kandidat...</p>');
            noCandidatesMessage.hide();
            selectBidangMessage.hide();
            selectAllCheckbox.prop('checked', false).prop('disabled', true);

            if (!parentId && !childId) {
                selectBidangMessage.show();
                candidatesCheckboxesDiv.empty();
                return;
            }

            let params = new URLSearchParams();
            if (childId) params.append('bidang_id', childId);
            else if (parentId) params.append('parent_id', parentId);
            if (year) params.append('tahun', year);

            try {
                const response = await fetch(`${BASE_URL}/get_candidates?${params.toString()}`);
                const candidates = await response.json();
                candidatesCheckboxesDiv.empty();
                if (response.ok) {
                    if (candidates.length === 0) {
                        noCandidatesMessage.show();
                    } else {
                        selectAllCheckbox.prop('disabled', false);
                        candidates.forEach(candidate => {
                            const itemHtml = `
                                <div class="candidate-item">
                                    <input type="checkbox" id="candidate-${candidate.id}" value="${candidate.id}" class="form-check-input">
                                    <label for="candidate-${candidate.id}" class="ms-2">${candidate.nama}</label>
                                    <span class="tahun">(${candidate.tahun_daftar})</span>
                                </div>`;
                            candidatesCheckboxesDiv.append(itemHtml);
                        });
                    }
                }
            } catch (error) { console.error('Network error fetching candidates:', error); }
        }
        
        async function loadYears() {
            yearFilter.html('<option value="">Semua Tahun</option>');
            try {
                const response = await fetch(`${BASE_URL}/get_unique_years`);
                const years = await response.json();
                if (response.ok) {
                    years.forEach(year => {
                        yearFilter.append(new Option(year, year));
                    });
                }
            } catch (error) { console.error('Error loading years:', error); }
        }

        // Inisialisasi halaman
        loadParentBidang();
        loadYears();

        // Event Listeners
        parentBidangFilter.on('change', async function() {
            currentActiveParentId = $(this).val();
            currentActiveChildId = null;
            resultsPanel.hide();
            infoBox.hide();
            yearFilter.val(''); // Reset filter tahun
            matchSelectedButton.prop('disabled', true);
            await loadChildBidang(currentActiveParentId);
            await loadCandidates(currentActiveParentId, '');
        });
        
        childBidangFilter.on('change', async function() {
            currentActiveChildId = $(this).val();
            resultsPanel.hide();
            if (currentActiveChildId) {
                await loadProfilAndBobot(currentActiveChildId);
                matchSelectedButton.prop('disabled', false);
            } else {
                infoBox.hide();
                matchSelectedButton.prop('disabled', true);
            }
            await loadCandidates(currentActiveParentId, currentActiveChildId, yearFilter.val());
        });

        yearFilter.on('change', function() {
            loadCandidates(currentActiveParentId, currentActiveChildId, $(this).val());
            resultsPanel.hide();
        });

        selectAllCheckbox.on('change', function() {
            $('#candidatesCheckboxes input[type="checkbox"]').prop('checked', this.checked);
        });

        matchSelectedButton.on('click', async function() {
            let bidangUntukHitung = currentActiveChildId;
            if (!bidangUntukHitung) return alert("Harap pilih Sub-Bidang spesifik untuk memulai perhitungan.");
            
            resultsPanel.show();
            candidatesListDiv.empty();
            loadingSpinner.show();
            saveResultsButton.hide();
            saveMessage.text('');
            
            const selectedIds = $('#candidatesCheckboxes input:checked').map((_, el) => el.value).get();
            if (selectedIds.length === 0) {
                loadingSpinner.hide();
                alert('Pilih setidaknya satu kandidat untuk dihitung.');
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/match_selected_candidates`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ candidate_ids: selectedIds, bidang_id: bidangUntukHitung })
                });
                const results = await response.json();
                if (response.ok && !results.error) {
                    lastMatchingResults = results;
                    let rank = 1;
                    results.forEach(candidate => {
                        let detailHtml = activeCriteriaKeys.map(key => 
                            `<li><strong>${toTitleCase(key)}:</strong> ${candidate.detail_kandidat[key] || 'N/A'}</li>`
                        ).join('');
                        let penjelasanHtml = candidate.penjelasan_skor.map(item => `<li>${item}</li>`).join('');
                        let rankClass = rank <= 3 ? `rank-${rank}` : 'rank-other';
                        const cardHtml = `
                            <div class="candidate-card ${rankClass}">
                                <div class="card-header-result">
                                    <div class="d-flex align-items-center">
                                        <span class="rank-badge ${rankClass}">${rank}</span>
                                        <span class="candidate-name">${candidate.nama}</span>
                                    </div>
                                    <span class="candidate-score">${candidate.skor_kecocokan}%</span>
                                </div>
                                <div class="card-body-result">
                                    <div class="row">
                                        <div class="col-md-6"><strong>Data Kriteria:</strong><ul>${detailHtml}</ul></div>
                                        <div class="col-md-6"><strong>Rincian Perhitungan:</strong><ul>${penjelasanHtml}</ul></div>
                                    </div>
                                </div>
                            </div>`;
                        candidatesListDiv.append(cardHtml);
                        rank++;
                    });
                    saveResultsButton.show();
                } else {
                    candidatesListDiv.html(`<p class="text-danger">Error: ${results.error || 'Terjadi kesalahan.'}</p>`);
                }
            } catch(error) {
                console.error('Error:', error);
            } finally {
                loadingSpinner.hide();
            }
        });
        
        saveResultsButton.on('click', async function() {
            if (lastMatchingResults.length === 0) return;
            $(this).prop('disabled', true).text('Menyimpan...');
            saveMessage.text('Sedang menyimpan...').css('color', 'blue');
            try {
                const response = await fetch(`${BASE_URL}/save_matching_results`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(lastMatchingResults)
                });
                const data = await response.json();
                if (response.ok) {
                    saveMessage.text(data.message).css('color', 'green');
                    lastMatchingResults = []; 
                } else {
                    saveMessage.text(`Gagal: ${data.error}`).css('color', 'red');
                }
            } catch(error) {
                saveMessage.text('Error koneksi saat menyimpan.').css('color', 'red');
            } finally {
                $(this).prop('disabled', false).text('Simpan Semua Hasil');
            }
        });
    });
    </script>
</body>
</html>
