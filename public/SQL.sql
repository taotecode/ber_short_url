-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2020-02-20 13:06:41
-- 服务器版本： 10.1.37-MariaDB
-- PHP 版本： 7.3.1

SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `dwz`
--

-- --------------------------------------------------------

--
-- 表的结构 `black`
--

CREATE TABLE `black` (
  `id` bigint(20) NOT NULL,
  `url` text COMMENT '黑名单url',
  `ip` text COMMENT '黑名单ip'
) ;

-- --------------------------------------------------------

--
-- 表的结构 `url`
--

CREATE TABLE `url` (
  `id` bigint(20) NOT NULL,
  `user` bigint(20) NOT NULL,
  `ip` text NOT NULL COMMENT '生成Ip',
  `url` text NOT NULL COMMENT '原始链接',
  `url_coded` text NOT NULL COMMENT '短链编码',
  `url_type` int(11) NOT NULL DEFAULT '1' COMMENT '短链方式',
  `visit` bigint(20) NOT NULL DEFAULT '0' COMMENT '访问次数',
  `time` datetime NOT NULL COMMENT '生成时间'
) ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `user` bigint(20) NOT NULL,
  `pass` text NOT NULL,
  `status` text COMMENT '身份'
) ;

--
-- 转储表的索引
--

--
-- 表的索引 `black`
--
ALTER TABLE `black`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `url`
--
ALTER TABLE `url`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user`),
  ADD UNIQUE KEY `user` (`user`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `black`
--
ALTER TABLE `black`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `url`
--
ALTER TABLE `url`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
