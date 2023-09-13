<?php

session_start();
if (!isset($_SESSION['tomb'])) {
    $_SESSION["tomb"] = [];
}
// $_SESSION["kiirtertek"] = 0;

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

<!-- 
    problémák:
        - frissítési gondok vannak
        - karakterkódolási gondok, ékezetes betűk
-->

<body>
    <main class="container-lg py-5">
        <section class="row">
            <article class="col">
                <form method="post" class="container-lg">
                    <?php
                    $megfejtendo = ['alma', 'barack', 'körte', 'dió', 'cseresznye', 'málna'];
                    $szorzo = 0;
                    $szo = '';
                    // $szo = [];

                    function nyerheto()
                    {
                        $randomertek = round(rand(1, 10)) * 100;
                        return $randomertek;
                    }

                    if (isset($_POST["spin"])) {
                        $_SESSION["kiirtertek"] = nyerheto();
                    }

                    function megfejtes($megfejtendo)
                    {
                        $index = round(rand(0, count($megfejtendo) - 1));
                        $_SESSION["megfejtes"] = $megfejtendo[$index];
                        // ellenőrzésekhez kell
                        return $_SESSION["megfejtes"];
                    }

                    // start gomb lenyomásával kiválaszt véletlenszerűen egy szót a tömbből amit ki kell találni
                    if (isset($_POST["start"])) {

                        $szo = megfejtes($megfejtendo);

                        if (mb_strlen(trim($szo)) !== 0) {
                            for ($i = 0; $i < strlen($szo); $i++) {
                                $_SESSION["szo"][] = $szo[$i];
                            }
                        }

                        for ($i = 0; $i < mb_strlen($szo); $i++) {
                            $_SESSION["tomb"][] = '';
                        }
                    }

                    // ellenőrzésnél a $_SESSION["tomb"]-öt kéne ellenőrizni hogy nincs -e benne üres string? akkor meg van fejtve?


                    // start gomb
                    echo '<section class="row gy-3"><article class="col-auto"><div class="p-2"><input type="submit" class="form-control" name="start" id="start" value="Start"></div></article></section>';

                    echo '<hr>';

                    // pörget gomb
                    echo '<section class="row gy-3"><article class="col-auto"><div class="p-2"><input type="submit" class="form-control" name="spin" id="spin" value="Pörget"></div></article></section>';

                    // megjelenítő section
                    echo '<section class="row gy-3"><article class="col-auto"><div class="p-2">';
                    // a nyerhető összeg megjelenítése
                    echo '<h3>A nyerhető összeg: ' . $_SESSION["kiirtertek"] . '</h3>';

                    // a végösszeg megjelenítése
                    echo '<h3>A végösszeg: ' . $_SESSION["osszeg"] . '</h3>';

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
                            echo '<article class="col"><div class="p-2"><input type="submit" class="form-control" name="data" id="_' . $sorKey.$oszlopKey . '" value="' . $gomb . '"></div></article>';
                        }
                        echo '</section>';
                    }

                    // csak tesztre a $szo helyett a lenti feltételben!
                    // $_SESSION["szo"] = ['a', 'l', 'm', 'a'];

                    $tomb = $_SESSION["tomb"];

                    // frissítési gondok vannak
                    // header("location: tombos.php");
                    if ($_SERVER["REQUEST_METHOD"] === 'POST') {

                        if ($_POST["data"] === "Reset") {
                            unset($_SESSION["tomb"]);
                            // nyerhető és összeg értékek alaphelyzetbe állítása
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
                                        if (mb_strlen(trim($_SESSION["tomb"][$i])) === 0) {
                                            $_SESSION["tomb"][$i] = $_POST["data"];
                                            $szorzo += 1;
                                        }
                                        // kiírás vége
                                    }
                                }
                                // ciklus vége
                                $_SESSION["osszeg"] += $szorzo * $_SESSION["kiirtertek"];
                            }
                        }
                    }

                    // print_r($tomb);

                    for ($i = 0; $i < count($tomb); $i++) {
                        echo '<li>' . $tomb[$i] . '</li>';
                    }

                    ?>
                </form>
            </article>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js" integrity="sha512-9GacT4119eY3AcosfWtHMsT5JyZudrexyEVzTBWV3viP/YfB9e2pEy3N7WXL3SV6ASXpTU0vzzSxsbfsuUH4sQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>