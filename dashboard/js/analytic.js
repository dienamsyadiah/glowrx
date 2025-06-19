function getData() {
    // Inisialisasi chart untuk gender
    $.ajax({
        type: 'GET',
        url: '../php/get_analytic.php',
        data: {
            functionName: 'getDataGender',
        },
        success: function(response) {
            let data = JSON.parse(response);
            let counts = {
                'Laki-Laki': 0,
                'Perempuan': 0,
            };

            // Warna untuk setiap label
            let colors = {
                'Laki-Laki': '#709FB0',
                'Perempuan': 'rgba(255, 99, 132, 0.6)'
            };

            // Menghitung jumlah jenis kelamin
            data.forEach(function(pengguna) {
                if (pengguna.jenis_kelamin in counts) {
                    counts[pengguna.jenis_kelamin]++;
                }
            });

            const ctx = document.getElementById('genderChart').getContext('2d');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(counts),
                    datasets: [{
                        label: 'Jumlah Pengguna',
                        backgroundColor: Object.values(colors),
                        borderColor: Object.values(colors),
                        data: Object.values(counts),
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        datalabels: {
                            formatter: (value, context) => {
                                let sum = 0;
                                let dataArr = context.chart.data.datasets[0].data;
                                dataArr.forEach(data => {
                                    sum += data;
                                });
                                let percentage = (value * 100 / sum).toFixed(2) + "%";
                                return percentage;
                            },
                            color: '#fff',
                            display: 'auto',
                            font: {
                                size: 10 // Ubah ukuran font datalabels di sini
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching data: ", error);
        }
    });

    // Inisialisasi chart untuk jenis kulit
    $.ajax({
        type: 'GET',
        url: '../php/get_analytic.php',
        data: {
            functionName: 'getDataSkin',
        },
        success: function(response) {
            let data = JSON.parse(response);
            let counts = {
                'Normal': 0,
                'Kering': 0,
                'Berminyak': 0,
                'Kombinasi': 0
            };

            // Warna untuk setiap label
            let colors = {
                'Normal': 'rgba(150, 100, 232, 0.6)',
                'Kering': 'rgba(255, 99, 132, 0.6)',
                'Berminyak': 'rgba(250, 170, 232, 0.6)',
                'Kombinasi': 'rgba(180, 180, 132, 0.6)'
            };

            data.forEach(function(tipekulit) {
                if (tipekulit.tipe in counts) {
                    counts[tipekulit.tipe]++;
                }
            });

            const ctx = document.getElementById('skinTypeChart').getContext('2d');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(counts),
                    datasets: [{
                        label: 'Jenis Kulit',
                        backgroundColor: Object.values(colors),
                        borderColor: Object.values(colors),
                        data: Object.values(counts),
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        datalabels: {
                            formatter: (value, context) => {
                                let sum = 0;
                                let dataArr = context.chart.data.datasets[0].data;
                                dataArr.forEach(data => {
                                    sum += data;
                                });
                                let percentage = (value * 100 / sum).toFixed(2) + "%";
                                return percentage;
                            },
                            color: '#fff',
                            display: 'auto',
                            font: {
                                size: 10 // Ubah ukuran font datalabels di sini
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching data: ", error);
        }
    });

    // Ambil data bahan kecantikan dari PHP dan inisialisasi chart
    $.ajax({
        type: 'GET',
        url: '../php/get_analytic.php',
        data: {
            functionName: 'getBeautyIngredientsData',
        },
        success: function(response) {
            var data = JSON.parse(response);
            var labels = [];
            var jumlah_produk = [];
            var color = '#709FB0'; // Warna solid yang akan digunakan untuk seluruh bar chart

            data.forEach(function(item) {
                labels.push(item.nama_bahan);
                jumlah_produk.push(item.jumlah_produk);
            });

            // Menyiapkan konfigurasi bar chart
            var config = {
                type: 'bar',
                data: {
                    datasets: [{
                        data: jumlah_produk,
                        backgroundColor: color, // Menggunakan satu warna untuk semua bagian
                        label: 'Jumlah Produk'
                    }],
                    labels: labels
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Persentase Jumlah Produk Berdasarkan Bahan pokok yang di gunakan'
                        },
                        datalabels: {
                            display: false // Menonaktifkan data labels untuk bar chart ini
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 10 // Ubah ukuran font sumbu x di sini
                                }
                            },
                            grid: {
                                display: false // Menonaktifkan garis kotak-kotak pada sumbu x
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 10 // Ubah ukuran font sumbu y di sini
                                }
                            },
                            grid: {
                                display: false // Menonaktifkan garis kotak-kotak pada sumbu y
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            };

            // Menggambar bar chart
            var ctx = document.getElementById('BahanChart').getContext('2d');
            var BahanChart = new Chart(ctx, config);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching data: ", error);
        }
    });

    // Masalah kulit
    $.ajax({
        type: 'GET',
        url: '../php/get_analytic.php',
        data: {
            functionName: 'getDataSkinProblems',
        },
        success: function(response) {
            let data = JSON.parse(response);
            console.log("Data received:", data); // Debug: Log data yang diterima
            let labels = data.map(item => item.nama_masalah);
            let values = data.map(item => parseInt(item.jumlah_pengguna)); // Convert to integers
            console.log("Labels:", labels); 
            console.log("Values:", values); 
            let backgroundColors = [
                '#709FB0',
            ];
            let borderColors = [
                'rgba(176, 196, 222, 1)',
            ];

            // Mendefinisikan chart untuk masalah kulit sebagai bar chart
            var ctxProblems = document.getElementById('problemSkinChart').getContext('2d');
            var dataProblems = {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pengguna',
                    data: values,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            };

            var optionsProblems = {
                responsive: true,
                plugins: {
                    datalabels: {
                        display: false // Menonaktifkan data labels untuk bar chart ini
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 10 // Ubah ukuran font sumbu x di sini
                            }
                        },
                        grid: {
                            display: false // Menonaktifkan garis kotak-kotak pada sumbu x
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 10 // Ubah ukuran font sumbu y di sini
                            }
                        },
                        grid: {
                            display: false // Menonaktifkan garis kotak-kotak pada sumbu y
                        }
                    }
                }
            };

            new Chart(ctxProblems, {
                type: 'bar',
                data: dataProblems,
                options: optionsProblems,
                plugins: [ChartDataLabels]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching data for skin problems chart: ", error);
        }
    });
}

$(document).ready(function() {
    getData();
});
