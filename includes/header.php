<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Kasir - Warung Pondok Mengkudu' ?></title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        .sidebar-link.active {
            background: #1e40af;
            color: #fff;
        }
    </style>
</head>

<body class="min-h-screen flex" style="background: rgb(241, 245, 249);">