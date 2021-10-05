<!DOCTYPE html>
<html>
    <head>
        <title>Viesu grāmata</title>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
        <script>
            var app = angular.module('myApp', []);
            app.controller('myCtrl', function($scope, $http) {

                $scope.currentPage = 0;
                $scope.pageSize = 10;

                $http.get("Rewiw_table").then(function(response) {
                    $scope.content = response.data;
                    $scope.content_lenght= response.data.length;
                    },
                    function(response) {
                        $scope.content = "Something went wrong";
                    });

                
                $scope.pageCount = function(){
                    return (window.Math.ceil($scope.content_lenght / $scope.pageSize))
                }
                $scope.orderByMe = function(x, y) {
                    $scope.myOrderBy = x;
                    $scope.order=y;
                }
                $scope.orderByMe('-created_at', 'LIFO')
            });
            app.filter('startFrom', ['$timeout', function($timeout){
                return function(input, start){
                    start = +start;
                    if(!input) return;
                        return input.slice(start);
                }
            }]);
        </script>
        
    </head>
    <body ng-app="myApp" ng-controller="myCtrl"> 
        <div class="container">
            <main>
                <h1 class="mb-3">Viesu grāmata</h1>            
                <div class="container">
                    <form method="POST" name="myForm" action="/main/check">
                        @csrf 
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="myName" class="form-label">Lietotāja vārds <span class="text-muted">(Obligātais lauks)</span></label>
                                <input type="text" class="form-control" placeholder="Ievadiet lietotāja vārdu" id="myName"name="myName" ng-model="myName" required>
                                <span ng-show="myForm.myName.$touched && myForm.myName.$invalid">Lietotāja vārds ir obligāts</span>
                            </div>

                            <div class="col-12">
                                <label for="myEmail" class="form-label">E-pasts <span class="text-muted">(Obligātais lauks)</span></label>
                                <input type="email" class="form-control" placeholder="Ievadiet e-pastu" id="myEmail" name="myEmail" ng-model="myEmail" required>
                                <span ng-show="myForm.myEmail.$touched && myForm.myEmail.$invalid">e-pasts ir nekorekts</span>
                            </div>

                            <div class="col-12">
                                <label for="myUrl" class="form-label">Saite uz vietni <span class="text-muted">(Neobligātais lauks)</span></label>
                                <input type="url" class="form-control" placeholder="Ievadiet saiti uz vietni" id="myUrl" name="myUrl" ng-model="myUrl">
                                <span ng-show="myForm.myUrl.$invalid">Saite uz vietni ir nekorekta</span>
                            </div>

                            <div class="col-12">
                                <label for="myMessage" class="form-label">Ziņojuma teksts <span class="text-muted">(Obligātais lauks)</span></label>    
                                <textarea class="form-control" placeholder="Ievadiet ziņojuma tekstu" id="myMessage" name="myMessage" ng-model="myMessage" required></textarea>
                                <span ng-show="myForm.myMessage.$invalid">Lauks nav izpildīts</span>
                            </div>

                            <input class="form-control" name="browser" id="browser" type="hidden"
                                    value = <?php
                                    $user_agent = $_SERVER["HTTP_USER_AGENT"];
                                    if     (strpos($user_agent, "Trident/7" )) $browser =  "IE"     ;
                                    elseif (strpos($user_agent, "Firefox"   )) $browser =  "Firefox";
                                    elseif (strpos($user_agent, "Edg"       )) $browser =  "Edge"   ;
                                    elseif (strpos($user_agent, "Chrome"    )) $browser =  "Chrome" ;
                                    elseif (strpos($user_agent, "Opera"     )) $browser =  "Opera"  ;
                                    elseif (strpos($user_agent, "Safari"    )) $browser =  "Safari" ;
                                    else $browser = "Nezināms";
                                    echo $browser;
                                    ?>
                                    >
                            <input  class="form-control" name="IP_adress" id="IP_adress" type="hidden"
                                    value = <?php echo $_SERVER['REMOTE_ADDR']?>
                            >
                            
                            <div class="col-12">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key')}}"></div>
                            </div>
                            <div class="col-12">
                                <button type="submit" >Ievads</button>
                            </div>

                            <p>Šķirošana: @{{ order }} <span class="text-muted">(Lai pamainīt šķirošanas veidu uzspiediet attiecīgas kolonnas nosaukumu)</span></p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th ng-click="orderByMe('created_at', 'Pievienošanas datums')">Pievienošanas datums</th>    
                                        <th ng-click="orderByMe('myName', 'Lietotāja vārds')">Lietotāja vārds</th>
                                        <th>E-pasts</th>
                                        <th>Saite uz vietni</th>
                                        <th>Ziņojuma teksts</th>
                                        <th>Pārlūkus</th>
                                        <th>IP adrese</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="record in content | orderBy: myOrderBy | startFrom:currentPage*pageSize | limitTo:pageSize">
                                        <td>@{{ record.created_at | date}}</td>
                                        <td>@{{ record.myName }}</td>
                                        <td>@{{ record.myEmail }}</td>
                                        <td>@{{ record.myUrl }}</td>
                                        <td>@{{ record.myMessage }}</td>
                                        <td>@{{ record.browser }}</td>
                                        <td>@{{ record.IP_adress }}</td>
                                    </tr>
                                </tbody>                                
                            </table>
                            <div class="col-12" id="pagitationStrip">
                                <button ng-hide="currentPage == 0" ng-click="currentPage = 0"><<</button>
                                <button ng-hide="currentPage == 0" ng-click="currentPage = currentPage - 1"><</button>                                
                                @{{ currentPage + 1 }} / @{{ pageCount() }}
                                <button ng-hide="currentPage >= content.length/pageSize - 1" ng-click="currentPage = currentPage + 1">></button>
                                <button ng-hide="currentPage >= content.length/pageSize - 1" ng-click="currentPage = pageCount() - 1">>></button>
                            </div>
                            <div class="col-12">
                            </div>
                        </div>                       
                    </form>
                </div>            
            </main>
        </div>
    
</body>
</html>
