# <center>ber_Short_Url</center>
[Chinese] (http: / / AA) | [English] (http: / / AA)
This is a small personal station short link service developed by the main network technology team of the hospital. Functions include: short chain generation, short chain reduction, anti red link generation, anti red and common short chain mutual rotation.
Short links do not call other websites, but directly use the links of this website, which is convenient for maintenance, quick access, and diversified customization
This project is composed of jQuery + layui
##< center > demo station < / center >
[berf1.cn](http://berf1.cn)
#< center > install < / center >
Environment requirements: Apache, php5.6 +, MySQL
Nginx needs to configure pseudo static independently. Of course, you can rely on some websites to convert Apache pseudo static to nginx pseudo static online
1、 Put the contents of the public folder into the project root directory, or point to the site root directory like the public folder through other files, but you need to change the file path inside.
2、 Create a table in the database and import sql.sql from the public folder into the table you just created.
3、 Modify BER / db / init.php to configure the database connection.
Now that the installation is complete, you can visit your project.
#< center > short chain algorithm < / center >
We have configured two sets of algorithms in BER / coded.php for you to choose
The first algorithm is random number. You can specify how many bit strings, and then randomly generate * bit strings
The second is MD5 string segmentation, which is divided into 6 specified bits. If you know PHP better, how many bit strings can you customize
There is no need to introduce two simple algorithms
#< center > last < / center >
About copyright information: we attach great importance to copyright information. Although the project is very simple, we still hope that users can keep a copyright while opening or modifying the project: copyright belongs to: main network technology team
If there is any problem with the project, please submit it or leave a message on the blog [http://blog.berfen.com/39.html] (http://blog.berfen.com/39.html)
