document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reportChart').getContext('2d');
    const urlParams = new URLSearchParams(window.location.search);
    const filter = urlParams.get('filter') || 'bulanan';
    const year = urlParams.get('year') || new Date().getFullYear();
    
    fetch(`get_report_data.php?filter=${filter}&year=${year}`)
        .then(response => response.json())
        .then(data => {
            const reportChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dates,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: data.pemasukan,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            fill: false
                        },
                        {
                            label: 'Pengeluaran',
                            data: data.pengeluaran,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
});

let currentPage = 0;
const rowsPerPage = 10;
let currentFilter = 'all'; // Menyimpan filter saat ini

function filterTable(filter) {
    currentPage = 0;
    currentFilter = filter; // Memperbarui filter saat ini
    showPage(currentFilter, currentPage);
    document.querySelectorAll('.buttons button').forEach(btn => {
        btn.style.fontWeight = 'normal'; // Setel kembali ke teks biasa
    });

    // Tambahkan gaya teks tebal ke tombol yang sedang diklik
    document.getElementById('btn-' + filter).style.fontWeight = 'bold';
}

function showPage(filter, page) {
    const rows = document.querySelectorAll('#tableWrapper tbody tr');
    let filteredRows = Array.from(rows).filter(row => {                    
        const tipe = row.querySelector('td:nth-child(4)').textContent.toLowerCase().trim();
        return filter === 'all' || tipe === filter;
    });

    const start = page * rowsPerPage;
    const end = start + rowsPerPage;

    // Hide all rows first
    rows.forEach(row => row.style.display = 'none');
    // Display rows for the current page
    filteredRows.slice(start, end).forEach(row => row.style.display = '');

    // Apply color based on tipe
    filteredRows.forEach(row => {
        const tipe = row.querySelector('td:nth-child(4)').textContent.toLowerCase().trim();
        row.style.backgroundColor = '';
        row.style.color = '';
        if (tipe === 'pemasukan') {
            row.style.backgroundColor = '#d4edda'; // Hijau muda
            row.style.color = '#155724'; // Hijau tua
        } else if (tipe === 'pengeluaran') {
            row.style.backgroundColor = '#f8d7da'; // Merah muda
            row.style.color = '#721c24'; // Merah tua
        }
    });

    document.getElementById('prevButton').disabled = page === 0;
    document.getElementById('nextButton').disabled = end >= filteredRows.length;
}

document.getElementById('prevButton').addEventListener('click', function() {
    if (currentPage > 0) {
        currentPage--;
        showPage(currentFilter, currentPage); // Menggunakan filter saat ini
    }
});

document.getElementById('nextButton').addEventListener('click', function() {
    const rows = document.querySelectorAll('#tableWrapper tbody tr');
    let filteredRows = Array.from(rows).filter(row => {
        const tipe = row.querySelector('td:nth-child(4)').textContent.toLowerCase().trim();
        return currentFilter === 'all' || tipe === currentFilter; // Menggunakan filter saat ini
    });

    if ((currentPage + 1) * rowsPerPage < filteredRows.length) {
        currentPage++;
        showPage(currentFilter, currentPage); // Menggunakan filter saat ini
    }
});

filterTable('all');
