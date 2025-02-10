<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block Page Navigation</title>
    <script>
        // Variable de condition (vous pouvez la définir en fonction de votre logique)
        let shouldBlockNavigation = true;

        window.addEventListener('beforeunload', (event) => {
            if (shouldBlockNavigation) {
                // Message de confirmation (certains navigateurs l'ignorent et affichent un message par défaut)
                const confirmationMessage = 'You have unsaved changes. Are you sure you want to leave this page?';

                // Pour la plupart des navigateurs
                event.returnValue = confirmationMessage;

                // Pour les anciens navigateurs
                return confirmationMessage;
            }
        });
    </script>
</head>
<body>
    <h1>Block Page Navigation Example</h1>
    <p>Try to leave or refresh this page to see the confirmation dialog.</p>
    <button onclick="shouldBlockNavigation = false;">Allow Navigation</button>
</body>
</html>
