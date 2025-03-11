<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Inspection Créée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header,
        .footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .details {
            margin: 20px 0;
        }

        .details ul {
            list-style-type: none;
            padding: 0;
        }

        .details li {
            margin-bottom: 10px;
        }

        .details img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Nouvelle Inspection Créée</h1>
    </div>

    <div class="content">
        <p>Bonjour,</p>
        <p>Une nouvelle inspection a été créée avec les détails suivants :</p>

        <div class="details">
            <h2>Détails de l'Inspection</h2>
            <ul>
                <li><strong>ID de l'inspection :</strong> {{ $propertyInspection->id ?? 'N/A' }}</li>
                <li><strong>Propriété :</strong> {{ $propertyInspection->estate->name ?? 'N/A' }}</li>
                <li><strong>Statut :</strong> {{ $propertyInspection->status ?? 'N/A' }}</li>
                <li><strong>Commentaire :</strong> {{ $propertyInspection->comments ?? 'Aucun' }}</li>
                <li><strong>Date :</strong> {{ $propertyInspection->date ?? 'N/A' }}</li>
                <li><strong>Niveau :</strong> {{ $propertyInspection->level ?? 'N/A' }}</li>
                <li><strong>Type de pièce :</strong> {{ $propertyInspection->room_type ?? 'N/A' }}</li>
                <li><strong>Nombre de pièces :</strong> {{ $propertyInspection->room_count ?? 'N/A' }}</li>
                <li><strong>Informations supplémentaires :</strong> {{ $propertyInspection->additional_info ?? 'Aucune' }}</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>Merci d'utiliser notre application !</p>
    </div>
</body>

</html>