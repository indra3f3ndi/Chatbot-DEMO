<?php
$host = 'localhost';
$dbname = 'chatbot_db';
$username = 'root';  // sesuaikan dengan username database kamu
$password = '';  // sesuaikan dengan password database kamu

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = strtolower(trim($_POST['question']));

        // Hilangkan semua simbol non-alfanumerik
        $input = preg_replace('/[^a-z0-9\s]/', '', $input);

        // Daftar sinonim dan kata-kata non-pertanyaan
        $nonQuestionPhrases = [
            'oke', 'terima kasih', 'siap', 'baiklah', 'thanks', 
            'baik', 'ok', 'sip', 'tidak ada', 'g ada', 'gk ada', 'oh',
            'paham', 'mengerti', 'saya mengerti', 'aku mengerti'
        ];
        $synonyms = [
            'hi' => ['hello', 'hai', 'halo', 'hey', 'hei'],
            'thanks' => ['thank you', 'thx', 'terima kasih', 'makasih', 'tengkyu'],
            'bye' => ['goodbye', 'see you', 'sampai jumpa', 'dah'],
            'yes' => ['ya', 'iya', 'yup', 'yep', 'betul','boleh','ada'],
            'no' => ['tidak', 'enggak', 'nggak', 'gak', 'nope']
        ];

        // Fungsi untuk mendapatkan respon acak dari array
        function getRandomResponse($responses) {
            return $responses[array_rand($responses)];
        }

        // Fungsi untuk memeriksa apakah input cocok dengan sinonim
        function matchesSynonym($input, $synonymKey, $synonyms) {
            return $input === $synonymKey || in_array($input, $synonyms[$synonymKey]);
        }

        // Respon untuk salam
        $greetings = [
            "Halo! Ada yang bisa saya bantu hari ini?",
            "Selamat datang! Apa yang ingin Anda tanyakan?",
            "Hai there! Senang bertemu dengan Anda. Ada yang bisa saya bantu?",
            "Halo! Saya siap membantu Anda. Apa yang ingin Anda ketahui?"
        ];

        // Respon untuk ucapan terima kasih
        $thankYouResponses = [
            "Sama-sama! Senang bisa membantu Anda.",
            "Dengan senang hati! Ada lagi yang bisa saya bantu?",
            "Terima kasih kembali! Jangan ragu untuk bertanya lagi jika Anda membutuhkan bantuan.",
            "Saya senang bisa membantu. Semoga hari Anda menyenangkan!"
        ];

        // Respon untuk perpisahan
        $goodbyeResponses = [
            "Sampai jumpa! Semoga hari Anda menyenangkan.",
            "Selamat tinggal! Jangan ragu untuk kembali jika Anda memiliki pertanyaan lain.",
            "Terima kasih atas percakapannya. Sampai bertemu lagi!",
            "Goodbye! Senang bisa membantu Anda hari ini."
        ];

        // Respon untuk pertanyaan tentang identitas chatbot
        $identityResponses = [
            "Saya adalah asisten virtual yang dirancang untuk membantu Anda dengan berbagai pertanyaan.",
            "Saya adalah AI chatbot yang dibuat untuk memberikan informasi dan bantuan.",
            "Saya adalah program komputer yang dirancang untuk berinteraksi dan membantu pengguna seperti Anda.",
            "Saya adalah asisten digital yang siap membantu Anda dengan berbagai topik dan pertanyaan."
        ];

        // Respon untuk pertanyaan yang tidak dapat dijawab
        $cannotAnswerResponses = [
            "Maaf, saya tidak memiliki informasi untuk menjawab pertanyaan itu. Bisakah Anda mencoba bertanya dengan cara lain?",
            "Sayangnya, saya tidak memiliki data yang cukup untuk menjawab pertanyaan tersebut. Apakah ada topik lain yang ingin Anda tanyakan?",
            "Mohon maaf, saya tidak bisa memberikan jawaban yang akurat untuk pertanyaan itu. Mungkin kita bisa membahas hal lain?",
            "Saya minta maaf, tapi saya tidak memiliki informasi yang Anda cari. Apakah ada cara lain saya bisa membantu Anda?"
        ];

        // Respon untuk pertanyaan tentang kemampuan chatbot
        $capabilityResponses = [
            "Saya dapat membantu Anda dengan berbagai pertanyaan umum, memberikan informasi, dan melakukan perhitungan sederhana.",
            "Kemampuan saya meliputi menjawab pertanyaan, memberikan saran, dan membantu dengan berbagai topik umum.",
            "Saya dirancang untuk membantu dengan pencarian informasi, menjawab pertanyaan, dan memberikan bantuan umum.",
            "Saya bisa membantu Anda dengan banyak hal, mulai dari menjawab pertanyaan hingga memberikan saran. Apa yang ingin Anda ketahui?"
        ];

        // Logika untuk menentukan jenis respon
        if (matchesSynonym($input, 'hi', $synonyms)) {
            echo json_encode(['answer' => getRandomResponse($greetings)]);
        } elseif (matchesSynonym($input, 'thanks', $synonyms)) {
            echo json_encode(['answer' => getRandomResponse($thankYouResponses)]);
        } elseif (matchesSynonym($input, 'bye', $synonyms)) {
            echo json_encode(['answer' => getRandomResponse($goodbyeResponses)]);
        } elseif (matchesSynonym($input, 'yes', $synonyms)) {
            $yesResponses = [
                "Baik, saya mengerti. Ada lagi yang bisa saya bantu?",
                "Oke, terima kasih atas konfirmasinya. Apa langkah selanjutnya?",
                "Bagus! Apakah ada hal lain yang ingin Anda diskusikan?",
                "Saya senang mendengarnya. Apa yang ingin kita bahas sekarang?"
            ];
            echo json_encode(['answer' => getRandomResponse($yesResponses)]);
        } elseif (matchesSynonym($input, 'no', $synonyms)) {
            $noResponses = [
                "Baik, tidak masalah. Ada hal lain yang bisa saya bantu?",
                "Saya mengerti. Jika Anda berubah pikiran, saya di sini untuk membantu.",
                "Oke, tidak apa-apa. Jangan ragu untuk bertanya jika Anda membutuhkan sesuatu.",
                "Baiklah. Jika Anda memiliki pertanyaan lain, silakan beri tahu saya."
            ];
            echo json_encode(['answer' => getRandomResponse($noResponses)]);
        } elseif (strpos($input, 'siapa kamu') !== false || strpos($input, 'apa kamu') !== false) {
            echo json_encode(['answer' => getRandomResponse($identityResponses)]);
        } elseif (strpos($input, 'apa yang bisa kamu lakukan') !== false || strpos($input, 'kemampuan kamu') !== false) {
            echo json_encode(['answer' => getRandomResponse($capabilityResponses)]);
        } elseif (in_array($input, $nonQuestionPhrases)) {
            $responses = [
                "Terima kasih atas tanggapan Anda. Ada hal lain yang bisa saya bantu?",
                "Saya sangat menghargai tanggapan Anda. Semoga jadi mudah.",
                "Terima kasih! Jika ada yang ingin ditanyakan lagi, saya siap membantu.",
                "Saya senang bisa membantu. Ada lagi yang ingin Anda tanyakan?",
                "Baiklah, terima kasih.",
                "Senang membantu Anda.",
                "Apakah ada hal lain yang ingin Anda ketahui?",
                "Jika Anda memiliki pertanyaan lain, jangan ragu untuk bertanya.",
                "Saya di sini jika Anda membutuhkan bantuan lebih lanjut.",
                "Terima kasih atas waktunya. Jangan sungkan untuk bertanya lagi.",
                "Semoga informasi yang saya berikan bermanfaat. Ada lagi yang bisa saya bantu?",
                "Saya senang bisa membantu. Jika ada pertanyaan lain, silakan sampaikan."
            ];
            echo json_encode(['answer' => getRandomResponse($responses)]);
        } else {
            // Pisahkan input menjadi kata-kata kunci
            $keywords = explode(' ', $input);
            $answers = [];
            $combinedAnswer = '';

            // Periksa setiap kata kunci untuk mencari jawaban
            foreach ($keywords as $keyword) {
                $stmt = $pdo->prepare("SELECT answer FROM knowledge_base WHERE question LIKE ?");
                $stmt->execute(['%' . $keyword . '%']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $answers[] = $result['answer'];
                }
            }

            if (count($answers) > 0) {
                // Gabungkan semua jawaban yang ditemukan
                $combinedAnswer = implode(' ', $answers);

                // Tambahkan pembuka dan penutup jawaban
                $intros = [
                    "Berdasarkan informasi yang saya temukan, ",
                    "Dari hasil pencarian, saya menemukan bahwa ",
                    "Setelah melihat data yang ada, ",
                    "Menurut data yang tersedia, ",
                    "Setelah saya cari, ",
                    "Dari sini dapat kita simpulkan, ",
                    "Dari berita yang saya temukan, ",
                    "Dari data yang ada, ",
                    "Berdasarkan analisis saya, ",
                    "Setelah memproses informasi yang ada, ",
                    "Dari pengetahuan yang saya miliki, ",
                    "Berdasarkan fakta-fakta yang tersedia, "
                ];
                $endings = [
                    ". Demikian penjelasan dari saya, semoga ini membantu.",
                    ". Saya harap penjelasan ini bermanfaat bagi Anda.",
                    ". Itulah informasi yang dapat saya berikan, semoga bermanfaat.",
                    ". Semoga jawaban ini bisa menjawab pertanyaan Anda.",
                    ". Semoga informasi yang saya berikan bisa dimengerti.",
                    ". Jika Anda membutuhkan klarifikasi lebih lanjut, jangan ragu untuk bertanya.",
                    ". Apakah ada hal lain yang ingin Anda ketahui terkait topik ini?",
                    ". Saya harap ini membantu menjawab pertanyaan Anda.",
                    ". Jika Anda ingin informasi lebih detail, silakan beri tahu saya.",
                    ". Apakah penjelasan ini sudah cukup jelas?"
                ];

                $finalAnswer = getRandomResponse($intros) . $combinedAnswer . getRandomResponse($endings);
                echo json_encode(['answer' => $finalAnswer]);
            } else {
                echo json_encode(['answer' => getRandomResponse($cannotAnswerResponses)]);
            }
        }
    }

    // Bagian untuk menambahkan pertanyaan dan jawaban baru ke database
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_question']) && isset($_POST['new_answer'])) {
        $new_question = strtolower(trim($_POST['new_question']));
        $new_question = preg_replace('/[^a-z0-9\s]/', '', $new_question); // Hilangkan simbol dari pertanyaan baru
        $new_answer = trim($_POST['new_answer']);

        // Menyimpan pertanyaan dan jawaban baru ke dalam database
        $stmt = $pdo->prepare("INSERT INTO knowledge_base (question, answer, is_custom) VALUES (?, ?, TRUE)");
        $stmt->execute([$new_question, $new_answer]);

        $successResponses = [
            "Terima kasih atas kontribusi Anda. Jawaban baru telah berhasil disimpan.",
            "Jawaban baru Anda telah disimpan dan akan membantu di masa mendatang. Terima kasih!",
            "Kontribusi Anda sangat dihargai. Jawaban tersebut sekarang tersimpan dalam database.",
            "Jawaban Anda berhasil ditambahkan ke database. Terima kasih atas bantuan Anda!",
            "Informasi baru telah berhasil ditambahkan. Terima kasih atas partisipasi Anda dalam meningkatkan pengetahuan saya.",
            "Saya telah mempelajari sesuatu yang baru berkat Anda. Terima kasih atas kontribusinya!",
            "Database pengetahuan saya telah diperbarui dengan informasi yang Anda berikan. Terima kasih banyak!",
            "Terima kasih telah berbagi pengetahuan. Ini akan sangat membantu dalam menjawab pertanyaan di masa depan."
        ];
        echo json_encode(['success' => getRandomResponse($successResponses)]);
    }
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
?>