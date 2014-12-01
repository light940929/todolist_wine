# To Do List

There are  RESTful API and  UI for the simple To Do List.
This project is done using SLIM PHP Micro Framework + Apache + PHP + PHPUnit + MySQL + JQuery + Ajax + HTML + CSS.


## Configuration

Database: "db/todolist_wine.sql"<br>
MySQL-PHP connection: "include/config.php"<br>
Pre-requisites: LAMP Server (Ubuntu14.04, Apache2, MySQL5, PHP5)<br>

### Docker To Build

``` bash
$ docker build -t todolist_wine .
```

### Docker To Run

``` bash
# run docker apache php
$ CONTAINER=$(docker run -d -p 80 -p 3306 -v /your/path/to/serve:/var/www/html todolist_wine)
# get the http port
$ docker port $CONTAINER 80
0.0.0.0:49206
```

### Docker To access the database
``` bash
# get the mysql port
$ docker port $CONTAINER 3306
0.0.0.0:49205
$ mysql -hdockerhost -uroot -P 49205


##Testing Restful API

Command Line:  phpunit tests/api/v1/apiTest.php<br>
Chrome:  Advanced REST client extension (Postman)<br>

##Request

<table>
 <tr>
   <th>URL</th>
   <th>Method</th>
   <th>Parameters</th>
   <th>Description</th>
 </tr>
 <tr>
  <td>/register</td>
  <td>POST</td>
  <td>name,email,password</td>
  <td>User Registration</td>
 </tr>
 <tr>
  <td>/login</td>
  <td>POST</td>
  <td>email,password</td>
  <td>User Login</td>
 </tr>
  <tr>
  <td>/login/:user_id</td>
  <td>DELETE</td>
  <td></td>
  <td>User Logout</td>
 </tr> 
  <td>/categories</td>
  <td>GET</td>
  <td></td>
  <td>Fetching All Categories</td>
 </tr>
 <tr>
  <td>/categories</td>
  <td>POST</td>
  <td>category_name</td>
  <td>To Create a New category</td>
 </tr>
 <tr>
 <tr>
  <td>/categories/:id</td>
  <td>GET</td>
  <td></td>
  <td>Fetching A Single Category</td>
 </tr>
 <tr>
  <td>/categories/:id</td>
  <td>PUT</td>
  <td>name,status</td>
  <td>Updating a Single Category</td>
 </tr>
 <tr>
  <td>/categories/:id</td>
  <td>DELETE</td>
  <td>category</td>
  <td>Delete a Single Category</td>
 </tr>
 <tr> 
  <td>/categories/:id/lists</td>
  <td>GET</td>
  <td></td>
  <td>Fetching All Lists</td>
 </tr>
  <tr>
  <td>/categories/:id/lists</td>
  <td>POST</td>
  <td>list_name</td>
  <td>To Create a New List</td>
 </tr>
 <tr>
  <td>/categories/:id/lists/:list_id</td>
  <td>GET</td>
  <td></td>
  <td>Fetching A Single List</td>
 </tr>
 <tr>
  <td>/categories/:id/lists/:list_id</td>
  <td>PUT</td>
  <td>name,status</td>
  <td>Updating a Single List</td>
 </tr>
 <tr>
  <td>/categories/:id/lists/:list_id</td>
  <td>DELETE</td>
  <td></td>
  <td>Delete a Single List</td>
 </tr>
</table>

