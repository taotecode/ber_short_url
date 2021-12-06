# <cente> BER short URL short chain project </center >
## <center>V1.2</center>
##### <center>[Chinese](https://github.com/yuanzhumc/ber_Short_Url/blob/master/README.md)|[English](https://github.com/yuanzhumc/ber_Short_Url/blob/master/README_EN.md)</center >

This is a small personal station short link service developed by the main network technology team of the hospital. Functions include: short chain generation, short chain reduction, anti red link generation, anti red and common short chain mutual rotation.
Short links do not call other websites, but directly use the links of this website, which is convenient for maintenance, quick access, and diversified customization
This project is composed of jQuery + layui

# <center>update log </center>

V1.2
- Add background management on the original basis
- Add API call generation interface

V1.0
- Realize short chain generation, short chain reduction, short chain anti red, short chain mutual conversion

# <center> demo station </center>

[berf1.cn](http://berf1.cn)  does not provide background demonstration temporarily
API interface document: [https://www.kancloud.cn/yuanzhu/iapp/1511496](https://www.kancloud.cn/yuanzhu/iapp/1511496 "https://www.kancloud.cn/yuanzhu/iapp/1511496")

# <center> install </center >

Environment requirements: Apache, php7.1 +, MySQL 5.6.5 (advanced functions must be enabled, otherwise the import will fail. If the import fails, please contact me through [blog. Berfen. Com](https://blog.berfen.com "blog. Berfen. Com")

Nginx needs to be configured with pseudo static independently. Of course, you can rely on some websites to convert Apache pseudo static to nginx pseudo static online

1、 Place the contents of the public folder in the project root directory, or refer to the site root directory like the public folder through other files

Or directly take all the files in the public folder to the root directory of the website * * but you need to change the file path. * *

2、 Create a table in the database and import `SQL. sql` from the public folder to the table you just created.

3、 Modify **BER/db/init.PHP** to configure the database connection.
Now that the installation is complete, you can visit your project.

#### <center> background address </center >
Because I took all the public files out for installation, my address is'`http://abc.com/ber/admin / `, which can be accessed generally
Background default account password
**Account number **: ** 2144680883**
**Password **: ** 123456**
##### Basic configuration
Basically, all website configurations are in the file `BER/init.PHP`

# <center> short chain algorithm </center >

We have configured two sets of algorithms in BER / coded.php for you to choose
The first algorithm is random number. You can specify how many bit strings, and then randomly generate * bit strings

The second is MD5 string segmentation, which is divided into 6 specified bits. If you know PHP better, how many bit strings can you customize
There is no need to introduce two simple algorithms

# <center> last </center>

About copyright information: we attach great importance to copyright information. Although the project is very simple, we still hope that users can keep a copyright while opening or modifying the project: copyright belongs to: main network technology team

If there is any problem with the project, please submit it or leave a message on the blog [http://blog.berfen.com/39.html](http://blog.berfen.com/39.html)