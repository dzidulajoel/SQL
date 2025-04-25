<!DOCTYPE html>
<html>

<head>
        <title>Enregistrement Audio</title>
</head>

<body>
        <h1>Enregistrement Audio</h1>

        <button id="startButton">Démarrer l'enregistrement</button>
        <button id="stopButton" disabled>Arrêter l'enregistrement</button>
        <p id="recordingStatus">Prêt à enregistrer</p>


        <script>
                let mediaRecorder;
                let audioChunks = [];
                const startButton = document.getElementById('startButton');
                const stopButton = document.getElementById('stopButton');
                const recordingStatus = document.getElementById('recordingStatus');

                startButton.addEventListener('click', async () => {
                        try {
                                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                                mediaRecorder = new MediaRecorder(stream);
                                audioChunks = [];

                                mediaRecorder.ondataavailable = event => {
                                        if (event.data.size > 0) {
                                                audioChunks.push(event.data);
                                        }
                                };

                                mediaRecorder.onstart = () => {
                                        recordingStatus.textContent = 'Enregistrement en cours...';
                                        startButton.disabled = true;
                                        stopButton.disabled = false;
                                };

                                mediaRecorder.onstop = async () => {
                                        recordingStatus.textContent = 'Enregistrement terminé. Envoi en cours...';
                                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' }); // Choisir le type approprié
                                        const formData = new FormData();
                                        formData.append('audio_data', audioBlob, 'audio_enregistrement_' + Date.now() + '.webm');

                                        try {
                                                const response = await fetch('upload.php', {
                                                        method: 'POST',
                                                        body: formData
                                                });

                                                const result = await response.text();
                                                recordingStatus.textContent = result;
                                                startButton.disabled = false;
                                                stopButton.disabled = true;
                                        } catch (error) {
                                                recordingStatus.textContent = 'Erreur lors de l\'envoi : ' + error;
                                                startButton.disabled = false;
                                                stopButton.disabled = true;
                                        }
                                };

                                mediaRecorder.start();
                        } catch (error) {
                                recordingStatus.textContent = 'Erreur lors de l\'accès au microphone : ' + error;
                        }
                });

                stopButton.addEventListener('click', () => {
                        if (mediaRecorder && mediaRecorder.state === 'recording') {
                                mediaRecorder.stop();
                        }
                });
        </script>
</body>

</html>