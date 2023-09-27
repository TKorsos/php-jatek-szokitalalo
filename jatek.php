<?php

session_start();

// header('Content-Type: text/html; Charset = UTF-8');

if (!isset($_SESSION['tomb'])) {
    $_SESSION["tomb"] = [];
}

$megfejtendo = ['alma', 'barack', 'körte', 'dió', 'cseresznye', 'málna'];
$szorzo = 0;
$szo = '';
$valtozo = '';

function nyerheto()
{
    // csőd bevezetése - rand(1, 10) helyett rand(0, 10)
    $randomertek = round(rand(0, 10)) * 100;
    return $randomertek;
}

if (isset($_POST["spin"])) {
    $valtozo = nyerheto();

    // feltétel csődhöz  ~ 20230927
    if ($valtozo > 0) {
        $_SESSION["kiirtertek"] = $valtozo;
    } else {
        $_SESSION["kiirtertek"] = 'Csőd';
        $_SESSION["osszeg"] = 0;
    }

    header('location: ' . $_SERVER['PHP_SELF']);
}


// nyerési feltételhez ~ 20230927
foreach ($_SESSION["tomb"] as $tomb) {
    if (mb_strlen(trim($tomb), 'UTF-8') !== 0) {
        $ures += 1;
    }
}

function megfejtes($megfejtendo)
{
    $index = round(rand(0, count($megfejtendo) - 1));
    $_SESSION["megfejtes"] = $megfejtendo[$index];
    return $_SESSION["megfejtes"];
}

// start gomb lenyomásával kiválaszt véletlenszerűen egy szót a tömbből amit ki kell találni
if (isset($_POST["start"])) {
    $szo = megfejtes($megfejtendo);

    if (mb_strlen(trim($szo), 'UTF-8') !== 0) {
        // preg_split metódus alkalmazása  ~ 20230918
        $container = preg_split('//u', $szo, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($container); $i++) {
            $_SESSION["szo"][$i] = $container[$i];
        }
    }

    for ($i = 0; $i < mb_strlen($szo, 'UTF-8'); $i++) {
        $_SESSION["tomb"][] = '';
    }

    header('location: ' . $_SERVER['PHP_SELF']);
}

// ellenőrzés hogy volt e küldés post-tal majd hogy milyen gomb lett lenyomva és ha megfelelő akkor "írja ki" a megfelelő helyre
if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    if ($_POST["data"] === "Reset") {
        unset($_SESSION["tomb"]);
        unset($_SESSION["kiirtertek"]);
        unset($_SESSION["osszeg"]);
        unset($_SESSION["szo"]);
    } else {
        if (isset($_POST["data"])) {
            // ciklus kezdete
            for ($i = 0; $i < count($_SESSION["szo"]); $i++) {
                // egyezőség feltétele
                if ($_SESSION["szo"][$i] === $_POST["data"]) {
                    // kiírás feltétele
                    if (mb_strlen(trim($_SESSION["tomb"][$i]), 'UTF-8') === 0) {
                        $_SESSION["tomb"][$i] = $_POST["data"];
                        $szorzo += 1;
                    }
                    // kiírás vége
                }
            }
            // ciklus vége
            (int)@$_SESSION["osszeg"] += $szorzo * $_SESSION["kiirtertek"];
        }
    }

    header('location: ' . $_SERVER['PHP_SELF']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>PHP gyakorlás</title>
</head>

<body>
    <main class="container-lg py-5">
        <section class="row">
            <article class="col">
                <form method="post" class="container-lg">
                    <?php
                    // start gomb
                    echo '<section class="row gy-3"><article class="col-auto"><div class="p-2"><input type="submit" class="form-control" name="start" id="start" value="Start"></div></article></section>';

                    echo '<hr>';

                    // pörget gomb
                    echo '<section class="row gy-3"><article class="col-auto"><div class="p-2"><input type="submit" class="form-control" name="spin" id="spin" value="Pörget"></div></article></section>';

                    echo '<hr>';

                    // megjelenítő section
                    echo '<section class="row gy-3"><article class="col-auto"><div class="p-2">';
                    // a nyerhető összeg megjelenítése ~ 20230927
                    if (isset($_SESSION["kiirtertek"]) && $_SESSION["kiirtertek"] > 0) {
                        echo '<h3>A nyerhető összeg: ' . ($_SESSION["kiirtertek"] ?? '') . '</h3>';
                    } elseif ($_SESSION["kiirtertek"] === 'Csőd') {
                        echo '<h3>A nyerhető összeg: ' . ($_SESSION["kiirtertek"] ?? '') . '</h3>';
                    } else {
                        echo '<h3>A nyerhető összeg: </h3>';
                    }

                    // a végösszeg megjelenítése
                    echo '<h3>A végösszeg: ' . ($_SESSION["osszeg"] ?? '') . '</h3>';

                    echo '</div></article></section>';
                    // megjelenítő section vége

                    echo '<hr>';

                    $gombok = [
                        ['a', 'b', 'c', 'd', 'e', 'f', 'g'],
                        ['h', 'i', 'j', 'k', 'l', 'm', 'n'],
                        ['o', 'p', 'q', 'r', 's', 't', 'u'],
                        ['v', 'w', 'x', 'y', 'z', 'á', 'é'],
                        ['í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű'],
                        ['Reset']
                    ];

                    foreach ($gombok as $sorKey => $sor) {
                        echo '<section class="row gy-3">';
                        foreach ($sor as $oszlopKey => $gomb) {
                            echo '<article class="col"><div class="p-2"><input type="submit" class="form-control" name="data" id="_' . $sorKey . $oszlopKey . '" value="' . $gomb . '"></div></article>';
                        }
                        echo '</section>';
                    }

                    echo '<hr>';

                    $tomb = $_SESSION["tomb"];

                    // a megfejtett betűk kiíratása
                    for ($i = 0; $i < count($tomb); $i++) {
                        echo '<li>' . $tomb[$i] . '</li>';
                    }

                    echo '<hr>';

                    // siker esetén üzenet kiíratása ~ 20230927
                    if (isset($ures) && $ures === count($_SESSION["szo"])) {
                        echo '<h2>Nyertél!</h2><h3>A végső nyereményed: ' . ($_SESSION["osszeg"] ?? '') . '</h3>';
                    }

                    ?>
                </form>
            </article>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js" integrity="sha512-9GacT4119eY3AcosfWtHMsT5JyZudrexyEVzTBWV3viP/YfB9e2pEy3N7WXL3SV6ASXpTU0vzzSxsbfsuUH4sQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>