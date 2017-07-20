<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Lokisalle</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class=""><a href="#">Link</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>Connexion</b><span class="caret"></span></a>
          <ul id="login-dp" class="dropdown-menu">
            <li>
              <div class="row">
                <div class="col-md-12">
                  <form class="form" role="form" method="post" action="" accept-charset="UTF-8" id="login-nav">
                      <legend>Login</legend>
                      <div class="form-group">
                        <label class="sr-only" for="exampleInputEmail2">Adresse Email</label>
                        <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Adresse Email" name="email" required>
                      </div>
                      <div class="form-group">
                        <label class="sr-only" for="exampleInputPassword2">Mot de passe</label>
                        <input type="password" class="form-control" id="exampleInputPassword2" name="mdp" placeholder="Mot de passe" required>
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Connexion</button>
                      </div>
                  </form>
                </div><!-- /.col-12 -->
                <div class="bottom text-center">
                  Nouveau ? <a href="inscription.php"><b>Inscris Toi</b></a>
                </div>
              </div><!-- /.row  -->
            </li>
          </ul><!-- /.dropdown-menu-->
        </li><!-- /.dropdown -->
      </ul><!-- /.navbar-right -->
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
