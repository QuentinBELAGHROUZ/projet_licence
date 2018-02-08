<?php session_start(); ?>
<?php include '../includes/config_bdd.php'; ?>
<?php include '../includes/templates/header.php'; ?>

<style>
body{margin-top:50px;}
.glyphicon { margin-right:10px; }
.panel-body { padding:0px; }
.panel-body table tr td { padding-left: 15px }
.panel-body .table {margin-bottom: 0px; }
</style>

<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-3">
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="glyphicon glyphicon-folder-close">
                            </span>Santé</a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <table class="table">
                            <tr>
                                    <td>
                                        <a href="#">Pharmacie</a>
                                        <span class="label label-info">2</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">Médecine</a>
                                        <span class="label label-info">6</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">Kinésithérapie</a>
                                        <span class="label label-info">1</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">Podologie</a>
                                        <span class="label label-info">10</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span class="glyphicon glyphicon-th">
                            </span>Sciences</a>
                        </h4>
                    </div>  
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><span class="glyphicon glyphicon-user">
                            </span>Arts, lettres, langues et sciences humaines</a>
                        </h4>
                    </div>
                </div>
              
            </div>

            <form action="" class="search-form">
                <div class="form-group has-feedback">
            		<label for="search" class="sr-only"></label>
            		<input type="text" class="form-control" name="search" id="search" placeholder="rechercher...">
              		<span class="glyphicon glyphicon-search form-control-feedback"></span>
            	</div>
            </form>

        </div>


        <div class="col-sm-9 col-md-9">
            <div class="well">
                <h1>
                    Accordion Menu With Icon</h1>
                Admin Dashboard Accordion Menu
            </div>
        </div>
    
    </div>
</div>
