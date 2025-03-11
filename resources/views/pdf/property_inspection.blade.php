<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'Inspection de Propriété</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 20px; }
        .section h2 { margin-bottom: 10px; }
        .content { margin: 0 40px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; }
        .image { max-width: 100px; height: auto; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VIEGAS Real Estate</h1>
        <p>Schledestraat 48.01 - 1080 Sint Jans Molenbeek</p>
    </div>

    <div class="content">
        <h1>Rapport d'Inspection de Propriété</h1>
        <h2>{{ $estate->title }}</h2>
        <p><strong>Location:</strong> {{ $estate->location }}</p>
        <p><strong>Description:</strong> {{ $estate->description }}</p>
        <p><strong>Prix:</strong> {{ $estate->price }}</p>

        @foreach($groupedReactions as $group)
            <h3>{{ $group['estate']['title'] }}</h3>
            <p><strong>Nom:</strong> {{ $group['estate']['name'] ?? 'Non spécifié' }}</p>

            <h3>Réactions et Configurations</h3>
            @foreach($group['reactions'] as $reaction)
                <div class="section">
                    <h4>Configuration ID : {{ $reaction['estate_configuration']->id }}</h4>
                    <p><strong>Niveau :</strong> {{ $reaction['estate_configuration']->level }}</p>
                    <p><strong>Type de pièce :</strong> {{ $reaction['estate_configuration']->room_type ?? 'Non spécifié' }}</p>
                    <p><strong>Nombre de pièces :</strong> {{ $reaction['estate_configuration']->room_count }}</p>
                    <p><strong>Informations supplémentaires :</strong> {{ $reaction['estate_configuration']->additional_info ?? 'Aucune' }}</p>
                    <p><strong>Détails :</strong> {{ $reaction['estate_configuration']->details ?? 'Aucun' }}</p>
                    <p><strong>Pièces :</strong></p>
                    <ul>
                        @foreach(json_decode($reaction['estate_configuration']->pieces, true) as $piece)
                            <li>{{ $piece['type'] }}: {{ $piece['nombre'] }}</li>
                        @endforeach
                    </ul>
                    <p><strong>Commentaire :</strong> {{ $reaction['comment'] ?? 'Aucun commentaire' }}</p>
                    @if($reaction['photo_url'])
                        <img src="{{ $reaction['photo_url'] }}" class="image" alt="Photo de la réaction">
                    @else
                        <p class="text-gray-400 italic">Aucune photo</p>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>

    <div class="footer">
        <p>Paraphes :</p>
    </div>
</body>
</html>