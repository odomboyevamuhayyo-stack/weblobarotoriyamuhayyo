<?php
$conn = new mysqli("localhost", "root", "", "habit_pro");

if ($conn->connect_error) {
    die("Xatolik!");
}

// ADD
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $emoji = $_POST['emoji'];
    $date = date("Y-m-d");

    $conn->query("INSERT INTO habits (user_id, title, emoji, created_at) 
    VALUES (1, '$title', '$emoji', '$date')");
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM habits WHERE id=$id");
}

// COMPLETE
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $date = date("Y-m-d");

    $check = $conn->query("SELECT * FROM habit_logs 
    WHERE habit_id=$id AND completed_date='$date'");

    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO habit_logs (habit_id, completed_date) 
        VALUES ($id, '$date')");
    }
}

// STATISTICS
$total = $conn->query("SELECT COUNT(*) as c FROM habits")->fetch_assoc()['c'];
$done = $conn->query("SELECT COUNT(*) as c FROM habit_logs")->fetch_assoc()['c'];
$percent = $total ? round(($done/$total)*100) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Habit Tracker 💖</title>
    <style>
        body {
            font-family: Arial;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            text-align: center;
        }

        .container {
            width: 400px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 20px;
        }

        input, select {
            padding: 10px;
            border-radius: 10px;
            border: none;
            margin: 5px;
        }

        button {
            padding: 10px;
            background: hotpink;
            border: none;
            color: white;
            border-radius: 10px;
        }

        .habit {
            background: #ffe4e1;
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
        }

        .progress {
            background: #eee;
            border-radius: 10px;
        }

        .bar {
            background: hotpink;
            padding: 10px;
            border-radius: 10px;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🌸 Habit Tracker</h1>

    <!-- ADD -->
    <form method="POST">
        <input type="text" name="title" placeholder="Odat yozing..." required>
        <select name="emoji">
            <option>💧</option>
            <option>📚</option>
            <option>🏃</option>
            <option>🧘</option>
            <option>🔥</option>
        </select>
        <button name="add">➕</button>
    </form>

    <!-- LIST -->
    <h3>📋 Odatlar</h3>

    <?php
    $res = $conn->query("SELECT * FROM habits");

    while ($row = $res->fetch_assoc()) {
        echo "<div class='habit'>";
        echo "{$row['emoji']} {$row['title']}<br>";

        echo "
        <a href='?complete={$row['id']}'>✔️</a>
        <a href='?delete={$row['id']}'>❌</a>
        ";

        echo "</div>";
    }
    ?>

    <!-- STAT -->
    <h3>📊 Statistika</h3>

    <p>$percent% bajarilgan 💖</p>

    <div class="progress">
        <div class="bar" style="width:<?= $percent ?>%">
            <?= $percent ?>%
        </div>
    </div>

    <!-- HISTORY -->
    <h3>📅 History</h3>

    <?php
    $history = $conn->query("
    SELECT habits.title, habits.emoji, habit_logs.completed_date 
    FROM habit_logs
    JOIN habits ON habits.id = habit_logs.habit_id
    ORDER BY completed_date DESC
    ");

    while ($row = $history->fetch_assoc()) {
        echo "<div>{$row['completed_date']} - {$row['emoji']} {$row['title']}</div>";
    }
    ?>
</div>

</body>
</html>