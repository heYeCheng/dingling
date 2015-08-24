/**
 * Created by victor on 15-2-8.
 */
//
var http = require('http');
var fs = require('fs');
var path = require('path');
var util = require('util');


http.createServer(function(req, res){

    var filePath ='.'+ req.url;
    console.log(req.url);
    if(filePath == './'){
        filePath = './index.html'
    }
    var extname = path.extname(filePath);
    var contentType = 'text/html';
    var isbinary = false;
    switch(extname){
        case '.css':
            contentType = 'text/css';
            break;
        case '.js':
            contentType = 'text/javascript';
            break;
        case '.gif':
            contentType = 'img/gif';
            isbinary = true;
            break;
        case '.jpg':
            contentType = 'img/jpg';
            isbinary = true;
            break;
        case '.png':
            contentType = 'img/png';
            isbinary = true;
            break;
    }
    fs.exists(filePath, function(exists){
        if(exists){
            if(isbinary){
                fs.stat(filePath, function(err, stat){
                    var rs;
                    res.writeHead(200,{'Content-Type':contentType, 'Content-Length':stat.size});
                    rs = fs.createReadStream(filePath)
                    rs.pipe(res,function(err){
                        if(err){
                            throw(err);
                        }
                    })
                })
            }else{
                fs.readFile(filePath, function(err, content){
                    if(err){
                        res.writeHead(500);
                        res.end();
                    }else{
                        console.log(contentType);
                        res.writeHead(200, { 'Content-Type':contentType });
                        res.end(content, 'utf-8');
                    }
                })
            }
        }else{
            res.writeHead(404);
            res.end(filePath + ' ' + 'is not exist!');
        }
    })
}).listen(3000);
console.log('listening');


























//
//
//
//
//var http = require('http')
//var fs = require('fs')
//var path = require('path')
//var util = require('util')
//
//http.createServer(function (request, response) {
//
//    console.log('request starting...');
//
//    var filePath = '.' + request.url;
//    if (filePath == './')
//        filePath = './index.html';
//
//    var extname = path.extname(filePath);
//    console.log(extname);
//    var contentType = 'text/html';
//    var ifbinary = false;
//    switch (extname) {
//        case '.js':
//            contentType = 'text/javascript';
//            break;
//        case '.css':
//            contentType = 'text/css';
//            break;
//        case '.gif':
//            contentType = 'image/gif';
//            ifbinary = true;
//            break;
//        case '.png':
//            contentType = 'image/png';
//            ifbinary = true;
//            break;
//        case '.jpg':
//            contentType = 'image/jpeg';
//            ifbinary = true;
//            break;
//    }
//
//    path.exists(filePath, function(exists) {
//
//        if (exists) {
//            if (ifbinary) {
//                fs.stat(filePath, function(error, stat) {
//                    var rs;
//                    response.writeHead(200,{ 'Content-Type': contentType, 'Content-Length' : stat.size });
//                    rs = fs.createReadStream(filePath);
//                    util.pump(rs, response, function(err) {
//                        if(err) {
//                            throw err;
//                        }
//                    });
//                });
//            }
//            else {
//                fs.readFile(filePath, function(error, content) {
//                    if (error) {
//                        response.writeHead(500);
//                        response.end();
//                    }
//                    else {
//                        response.writeHead(200, { 'Content-Type': contentType });
//                        response.end(content, 'utf-8');
//                    }
//                });
//            }
//        }
//        else {
//            response.writeHead(404);
//            response.end();
//        }
//    });
//}).listen(8125);