<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation d'inscription à la newsletter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #FF7D68;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <h2>Bienvenue sur notre newsletter !</h2>
    
    <p>Bonjour,</p>
    
    <p>Merci de vous être inscrit à notre newsletter. Pour finaliser votre inscription et commencer à recevoir nos actualités, veuillez cliquer sur le bouton ci-dessous :</p>
    
    <a href="{{ $confirmationUrl }}" class="button">Confirmer mon inscription</a>
    
    <p>Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :</p>
    <p>{{ $confirmationUrl }}</p>
    
    <div class="footer">
        <p>Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.</p>
        <p>Pour vous désinscrire à tout moment : <a href="{{ $unsubscribeUrl }}">Cliquez ici</a></p>
    </div>
</body>
</html> 