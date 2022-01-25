-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2012 at 11:31 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `a`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `comment` varchar(800) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment`) VALUES
(3, 'I am in search of touch phone,Anyone who want to purchase can  contact me at munir@gmail.com'),
(4, 'I have an extra Monitor,I really don''t need it,In market it would cost 1000 PKR ,so its better if  anyone from you could take it from me for free.contact me at 0314-9088765'),
(5, 'Hello I am Rashid khan from Rawalpindi,I have books for class 7th and 8th Oxford course.Anyone who is willing can get from me.These are in good condition.My contact num is 0331-9940326'),
(6, 'For any type of tuition and Computer courses ,please contact us maju-courses@yahoo.com'),
(7, 'I want to sale my Laptop,It was given to me by Punjab government,but mjhy ab ye ni chaiye,Agar kisi ko chaiye to mjhy is num pe contact kary ..0332-7077612'),
(8, 'Guys who has exepertise in Web Designing can contact me on umer90@gmail.com.I have lot of Web Design work.Handsome salary will be given to selected person.'),
(9, 'Please in case of blood requirement,Please contact us.No foundations,only individual.We are willing to donate just for free.Thank you 0331-5000890');

-- --------------------------------------------------------

--
-- Table structure for table `complain`
--

CREATE TABLE IF NOT EXISTS `complain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` varchar(800) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `complain`
--

INSERT INTO `complain` (`id`, `comment`) VALUES
(1, 'We don''t want this type of education system in which only GPA matters when we go for a Job Interview.Ministry of Education must take appropriate steps to make things better'),
(2, 'We want educated people in Ministry,People like Abdul Qadir and other,They should be given chance to lead the country.'),
(3, 'I don''t want to criticize  here.but Media in our country is not performing its role properly,I think the channels like GEO are banned or they must be given Warning from PEMRA.'),
(4, 'I lost my shirt,anyone who can help me.. :P Adil'),
(5, 'I want to use this awesome platform  to say Eid Mubarak to everyone... :)'),
(6, 'This is a great platform to share our views,i want to say that we need revolution,these people cannot change our future,We need to make the judicial system fair and free of any illegal activity.'),
(7, 'Why did we open the supply for NATO,it must be banned.Zardari and CoAS are the real culprits of the nation. ');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` varchar(20) NOT NULL,
  `comment` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `votes`
--

