<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enigma</title>
</head>
<body>
    <h1>Enigma</h1>
    <form action="/encrypt" method="POST">
        @csrf
        <label for="text">暗号化するテキスト:</label>
        <input type="text" id="text" name="text">
        <button type="submit">暗号化</button>
    </form>

    <form action="/decrypt" method="POST">
        @csrf
        <label for="text">復号化するテキスト:</label>
        <input type="text" id="text" name="text">
        <button type="submit">復号化</button>
    </form>

    <form action="/db" method="GET">
        <button type="submit">データベースクエリ実行</button>
    </form>
</body>
</html>
