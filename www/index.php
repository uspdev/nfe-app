<?php
$uri = $_SERVER["REQUEST_URI"];
$ws_url = 'http://dev.set.eesc.usp.br/uspdev/nfe-ws/api';
$ws_usr = 'teste';
$ws_pwd = 'teste';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>NFE</title>
    <meta charset="utf-8">
    <base href="<?php echo $uri ?>"/>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="lib/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
    </style>
</head>
<body ng-app="myApp">
<div ng-controller="main">

    <div class="container-fluid">
        <form name="Form" ng-submit="submit()">

            <p>Webservice</p>
            <input type="text" ng-model="url" size="50" name="url"/><br><br>

            <p>Cole o xml aqui</p>
            <textarea name="xml" ng-model="xml" rows="10" cols="50"></textarea>

            <p>Ou a chave (somente numeros - 44 digitos)</p>
            <input type="text" ng-model="chave" size="50" name="chave"/>
            <br/><br/>
            <input type="submit" value="Enviar">
        </form>

        <div ng-view ng-show="res">
            <div ng-show="res.chave">Chave: {{res.chave}}</div>

            <div ng-repeat="(key,val) in res.status">
                {{key}}: {{val}}
            </div>
            <b>Links</b>
            <div ng-repeat="(key,val) in res.url">
                {{key}}: <a href="{{val}}">{{val}}</a>
            </div>
        </div>
    </div>
</div>

<script src="lib/jquery-2.1.1.min.js"></script>
<script src="components/angular/angular.js"></script>

<script type="text/javascript">
    angular.module('myApp', []);
    var app = angular.module('myApp');

    app.controller('main', function ($scope, $http) {
        // do something
        $scope.xml = '';
        $scope.chave = '';
        $scope.url = '<?php echo $ws_url ?>';
        $scope.ws_usr = '<?php echo $ws_usr ?>';
        $scope.ws_pwd = '<?php echo $ws_pwd ?>';


        $scope.submit = function () {
            if (!($scope.xml != '' || $scope.chave != '')) {
                alert('Campo xml e chave não podem ser vazios.');
            }
            else {
                var data = {};
                data.xml = $scope.xml;
                data.chave = $scope.chave;
                data.url = $scope.url;


                $http({
                    method: 'POST',
                    url: $scope.url + '/xml',
                    data: $.param(data),
                    headers:
                        {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'Authorization': 'Basic ' + btoa($scope.ws_usr + ':' + $scope.ws_pwd)
                        }
                }).then(function onSuccess(data) {
                    console.log(data);
                    $scope.res = data.data;
                    // this callback will be called asynchronously
                    // when the response is available
                }, function onError(data, status, headers, config) {
                    console.log(status);
                    // called asynchronously if an error occurs
                    // or server returns response with an error status.
                });
            }
        }
    });


</script>

</body>
</html>
