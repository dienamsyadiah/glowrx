$(document).ready(function() {
    $('#chatForm').on('submit', function(event) {
        event.preventDefault();
        var message = $('#message').val(); // Simpan pertanyaan pengguna sebelum dikirim

        // Ganti nama pengguna dengan nama lengkap dari pengguna yang sedang login
        var fullName = 'Me';

        // Tambahkan pesan ke kotak obrolan dengan nama lengkap dan kelas user-message
        $('#chatbox').append('<p class="user-message"><strong>' + fullName + ':</strong> ' + message + '</p>');

        $.ajax({
            url: 'response.php',
            method: 'POST',
            data: { message: message },
            dataType: 'json', // Tambahkan ini untuk memastikan respons diterima dalam format JSON
            success: function(response) {
                console.log('Response:', response); // Debug response
                // Ganti nama chatbot menjadi "Bot GlowRX" di dalam kotak obrolan
                var chatbotName = 'Bot GlowRX';
                // Tambahkan elemen baru untuk mengetik efek dengan kelas bot-message
                var newElement = $('<p class="bot-message"><strong>' + chatbotName + ':</strong> <span class="typing"></span></p>');
                $('#chatbox').append(newElement);
                typeEffect(response.response, newElement.find('.typing'));
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });

        // Hapus pertanyaan pengguna dari input
        $('#message').val('');
    });

    function typeEffect(text, target) {
        var i = 0;
        var speed = 100; 
        function typeWriter() {
            if (i < text.length) {
                target.append(text.charAt(i));
                i++;
                setTimeout(typeWriter, speed);
            }
        }
        typeWriter();
    }
});
