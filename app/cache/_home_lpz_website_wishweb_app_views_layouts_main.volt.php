<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="#">WISH</a>
            <?php echo $this->elements->getMenu(); ?>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $this->flash->output(); ?>
    <?php echo $this->getContent(); ?>
    <hr>
    <footer>
        <p>&copy; 304club 2014</p>
    </footer>
</div>
