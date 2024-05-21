
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="html 5 template">
    <meta name="author" content="">
    <title>Polo Palestrante</title>
    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- ARCHIVES CSS -->
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/lightcase.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/maps.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" id="color" href="css/default.css">
</head>

<body>
    <!-- START SECTION HEADINGS -->
    <?php include('header.php');?>

    <!-- STAR HEADER SEARCH -->
    <div id="map-container" class="fullwidth-home-map dark-overlay">
        <!-- Video -->
        <div class="video-container">
            <video poster="images/bg/video-poster.jpg" loop autoplay muted>
                <source src="video/video-home.mp4" type="video/mp4">
            </video>
        </div>
    </div>
    <!-- END HEADER SEARCH -->

    <!-- START SECTION TEMAS -->
    <section class="popular portfolio py-3">
        <div class="container-fluid">
            <div class="sec-title">
                <h2 data-aos="fade-right">Temas em Alta</h2>
                <p data-aos="fade-left">Encontre os temais mais procurados pelas maiores empresas e eventos do país</p>
            </div>
            <div class="portfolio col-xl-12">
                <div class="slick-temas">
                    <?php
                    $sql = "
                    SELECT h.titulo , h.descricao , h.caminhoImagem , h.link , ch.titulo categoria , ch.descricao descricaoCategoria
                    FROM pp_home h
                    INNER JOIN pp_categoriaHome ch ON (h.fkCategoriaHome = ch.pkId)
                    WHERE h.ativo = 'S'
                    AND ch.ordem = (SELECT ordem FROM pp_categoriaHome WHERE ativo = 'S' ORDER BY ordem LIMIT 0,1)
                    ORDER BY ch.ordem
                    ";
                    $query = mysqli_query($connecta,$sql);
                    $offset = 0;
                    while($row = mysqli_fetch_object($query)) {
                        echo '
                        <div data-aos="fade-up" data-aos-offset="'.$offset.'" class="agents-grid">
                            <div class="landscapes">
                                <div class="project-single">
                                    <div class="project-inner project-head">
                                        <div class="homes rounded-top">
                                            <!-- homes img -->
                                            <a href="'.$row->link.'" class="homes-img hover-effect">
                                                <img src="../area-administrativa/home/imagens/'.$row->caminhoImagem.'" alt="'.$row->titulo.'" class="img-responsive">
                                                <div class="overlay"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- homes content -->
                                    <a href="'.$row->link.'">
                                    <div class="homes-content">
                                        <!-- homes address -->
                                        <h3>'.$row->titulo.'</h3>
                                        <p class="homes-address">
                                            '.$row->descricao.'
                                        </p>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        ';
                        $offset = $offset + 10;
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION TEMAS -->

    <!-- START SECTION EXCLUSIVO -->
    <section class="popular portfolio py-3">
        <div class="container-fluid">
            <div class="row">
                <div data-aos="fade-right" class="col-xl-2 text-left mt-5 align-self-center">
                    <h2>Confira o Nosso Time Exclusivo</h2>
                    <p>Os melhores palestrantes do Brasil, exclusivos da Polo</p>
                    <a href="" class="btn btn-polo px-3 shadow fs-6">Visualize todos</a>
                </div>
                <div class="portfolio col-xl-10">
                    <div class="slick-temas">
                        <?php
                        $sql = "
                        SELECT h.titulo , h.descricao , h.caminhoImagem , h.link , ch.titulo categoria , ch.descricao descricaoCategoria
                        FROM pp_home h
                        INNER JOIN pp_categoriaHome ch ON (h.fkCategoriaHome = ch.pkId)
                        WHERE h.ativo = 'S'
                        AND ch.ordem = (SELECT ordem FROM pp_categoriaHome WHERE ativo = 'S' ORDER BY ordem LIMIT 1,1)
                        ORDER BY ch.ordem
                        ";
                        $query = mysqli_query($connecta,$sql);
                        $offset = 0;
                        while($row = mysqli_fetch_object($query)) {
                            echo '
                            <div data-aos="fade-left" data-aos-offset="'.$offset.'" class="agents-grid">
                                <div class="landscapes">
                                    <div class="project-single">
                                        <div class="project-inner project-head">
                                            <div class="homes rounded-top">
                                                <!-- homes img -->
                                                <a href="'.$row->link.'" class="homes-img hover-effect">
                                                    <img src="../area-administrativa/home/imagens/'.$row->caminhoImagem.'" alt="'.$row->titulo.'" class="img-responsive">
                                                    <div class="overlay"></div>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- homes content -->
                                        <a href="'.$row->link.'">
                                        <div class="homes-content">
                                            <!-- homes address -->
                                            <h3>'.$row->titulo.'</h3>
                                            <p class="homes-address">
                                                '.$row->descricao.'
                                            </p>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            ';
                            $offset = $offset + 10;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION EXCLUSIVO -->

    <!-- START SECTION PESQUISADOS -->
    <section class="popular portfolio py-3">
        <div class="container-fluid">
            <div class="sec-title">
                <h2 data-aos="fade-right">Mais pesquisados</h2>
                <p data-aos="fade-left">Encontre os palestrantes mais requisitados nos maiores eventos do país</p>
            </div>
            <div class="portfolio col-xl-12">
                <div class="slick-temas">
                    <?php
                    $sql = "
                    SELECT h.titulo , h.descricao , h.caminhoImagem , h.link , ch.titulo categoria , ch.descricao descricaoCategoria
                    FROM pp_home h
                    INNER JOIN pp_categoriaHome ch ON (h.fkCategoriaHome = ch.pkId)
                    WHERE h.ativo = 'S'
                    AND ch.ordem = (SELECT ordem FROM pp_categoriaHome WHERE ativo = 'S' ORDER BY ordem LIMIT 2,1)
                    ORDER BY ch.ordem
                    ";
                    $query = mysqli_query($connecta,$sql);
                    $offset = 0;
                    while($row = mysqli_fetch_object($query)) {
                        echo '
                        <div data-aos="fade-up" data-aos-offset="'.$offset.'" class="agents-grid">
                            <div class="landscapes">
                                <div class="project-single">
                                    <div class="project-inner project-head">
                                        <div class="homes rounded-top">
                                            <!-- homes img -->
                                            <a href="'.$row->link.'" class="homes-img hover-effect">
                                                <img src="../area-administrativa/home/imagens/'.$row->caminhoImagem.'" alt="'.$row->titulo.'" class="img-responsive">
                                                <div class="overlay"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- homes content -->
                                    <a href="'.$row->link.'">
                                    <div class="homes-content">
                                        <!-- homes address -->
                                        <h3>'.$row->titulo.'</h3>
                                        <p class="homes-address">
                                            '.$row->descricao.'
                                        </p>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        ';
                        $offset = $offset + 10;
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION PESQUISADOS -->

    <!-- START SECTION INOVE -->
    <section class="popular portfolio py-3">
        <div class="container-fluid">
            <div class="sec-title">
                <h2 data-aos="fade-right">Inove em Seu Evento</h2>
                <p data-aos="fade-left">Encontre atrações diferenciadas e supreenda em seu evento</p>
            </div>
            <div class="portfolio col-xl-12">
                <div class="slick-temas">
                    <?php
                    $sql = "
                    SELECT h.titulo , h.descricao , h.caminhoImagem , h.link , ch.titulo categoria , ch.descricao descricaoCategoria
                    FROM pp_home h
                    INNER JOIN pp_categoriaHome ch ON (h.fkCategoriaHome = ch.pkId)
                    WHERE h.ativo = 'S'
                    AND ch.ordem = (SELECT ordem FROM pp_categoriaHome WHERE ativo = 'S' ORDER BY ordem LIMIT 3,1)
                    ORDER BY ch.ordem
                    ";
                    $query = mysqli_query($connecta,$sql);
                    $offset = 0;
                    while($row = mysqli_fetch_object($query)) {
                        echo '
                        <div data-aos="fade-up" data-aos-offset="'.$offset.'" class="agents-grid">
                            <div class="landscapes">
                                <div class="project-single">
                                    <div class="project-inner project-head">
                                        <div class="homes rounded-top">
                                            <!-- homes img -->
                                            <a href="'.$row->link.'" class="homes-img hover-effect">
                                                <img src="../area-administrativa/home/imagens/'.$row->caminhoImagem.'" alt="'.$row->titulo.'" class="img-responsive">
                                                <div class="overlay"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- homes content -->
                                    <a href="'.$row->link.'">
                                    <div class="homes-content">
                                        <!-- homes address -->
                                        <h3>'.$row->titulo.'</h3>
                                        <p class="homes-address">
                                            '.$row->descricao.'
                                        </p>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        ';
                        $offset = $offset + 10;
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION INOVE -->

    <!-- START SECTION EXCLUSIVO -->
    <section class="popular portfolio py-3">
        <div class="container-fluid">
            <div class="row">
                <div data-aos="fade-right" class="col-xl-2 text-left mt-5 align-self-center">
                    <h2>Palestrantes que você precisa conhecer!</h2>
                    <p>Grandes palestrantes que talvez você ainda não conheça</p>
                    <a href="" class="btn btn-polo px-3 shadow fs-6">Visualize todos</a>
                </div>
                <div class="portfolio col-xl-10">
                    <div class="slick-temas">
                        <?php
                        $sql = "
                        SELECT h.titulo , h.descricao , h.caminhoImagem , h.link , ch.titulo categoria , ch.descricao descricaoCategoria
                        FROM pp_home h
                        INNER JOIN pp_categoriaHome ch ON (h.fkCategoriaHome = ch.pkId)
                        WHERE h.ativo = 'S'
                        AND ch.ordem = (SELECT ordem FROM pp_categoriaHome WHERE ativo = 'S' ORDER BY ordem LIMIT 4,1)
                        ORDER BY ch.ordem
                        ";
                        $query = mysqli_query($connecta,$sql);
                        $offset = 0;
                        while($row = mysqli_fetch_object($query)) {
                            echo '
                            <div data-aos="fade-right" data-aos-offset="'.$offset.'" class="agents-grid">
                                <div class="landscapes">
                                    <div class="project-single">
                                        <div class="project-inner project-head">
                                            <div class="homes rounded-top">
                                                <!-- homes img -->
                                                <a href="'.$row->link.'" class="homes-img hover-effect">
                                                    <img src="../area-administrativa/home/imagens/'.$row->caminhoImagem.'" alt="'.$row->titulo.'" class="img-responsive">
                                                    <div class="overlay"></div>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- homes content -->
                                        <a href="'.$row->link.'">
                                        <div class="homes-content">
                                            <!-- homes address -->
                                            <h3>'.$row->titulo.'</h3>
                                            <p class="homes-address">
                                                '.$row->descricao.'
                                            </p>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            ';
                            $offset = $offset + 10;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END SECTION EXCLUSIVO -->

    <!-- START FOOTER -->
    <?php include('footer.php'); ?>

    <a data-scroll href="#heading" class="go-up"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>
    <!-- END FOOTER -->

    <!-- ARCHIVES JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/transition.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/fitvids.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/smooth-scroll.min.js"></script>
    <script src="js/lightcase.js"></script>
    <script src="js/search.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/slick-polo.js"></script>
    <script src="js/script-polo.js"></script>

    <!-- MAIN JS -->
    <script src="js/script.js"></script>
    <script>
        AOS.init();
    </script>

</body>

</html>
