# <center>ber_Short_Url</center>
[中文](https://github.com/yuanzhumc/ber_Short_Url/blob/master/README.md)|[English](https://github.com/yuanzhumc/ber_Short_Url/blob/master/README_EN.md)
这是由院主网络科技团队开发的一款小型个人站短链接服务。功能有：短链生成，短链还原，防红链接生成，防红和普通短链互转。
短链接不调用其他网站，直接使用本站链接，方便维护，访问快捷，自定义多样化
本项目是由jquery+layui组成的
## <center>演示站</center>
[berf1.cn](http://berf1.cn)
# <center>安装</center>
环境要求：Apache、php5.6+、MySql
Nginx需要独立配置伪静态，当然，你可以依靠一些网站在线把apache伪静态转为nginx伪静态
一.将public文件夹的内容放入项目根目录，或通过其他文件将网站根目录指像public文件夹，但需要改一下里面的文件路径。
二.在数据库中创建一个表，将public文件夹里的sql.sql导入到刚刚创建的表。
三.修改ber/db/init.php，配置数据库连接。
现在就已经安装完成，可以访问你的项目看看啦。
# <center>短链算法</center>
我们在ber/Coded.php为你配置了两套算法，供您自主选择
第一个算法即随机数，你可以指定多少位字符串，然后随机生成*位字符串
第二个是md5字符串分割，分成指定6位，如果你比较了解PHP，你可以自定义多少位字符串
使用方法就不用介绍了，很简单的两种算法
# <center>最后</center>
关于版权信息：我们对于版权信息很重视，虽然项目很简单，但我们还是希望用户在二开或修改本项目的同时，能够保留一个版权：版权所属：院主网络科技团队
如果项目有什么问题，欢迎提交，或来到博客留言[http://blog.berfen.com/39.html](http://blog.berfen.com/39.html)
