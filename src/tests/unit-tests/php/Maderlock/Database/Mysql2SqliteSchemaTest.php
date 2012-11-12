<?php

namespace Maderlock\Database;

require(dirname(__FILE__) . '/../../../../../../vendor/autoload.php');

class Mysql2SqliteSchemaTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->object = new \Maderlock\Database\Mysql2SqliteSchema();
    }

    /**
     * @test
     * @dataProvider convertProvider
     */
    public function testConvert($input, $expected, $message)
    {
        $output = $this->object->convertSchema($input);

        $this->assertEquals($expected, $output, $message);
    }

    public function convertProvider()
    {
        return array(
            array(
                "CREATE TABLE `fruit_time` (
  `type` tinyint(4) NOT NULL,
  `taste` int(11),
  `datePicked` datetime,
  `job` varchar(255)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DEFAULT PARTITION BY SUBPARTITIONS 10;",
                "CREATE TABLE 'fruit_time' (
  'type' tinyint(4) NOT NULL,
  'taste' int(11),
  'datePicked' datetime,
  'job' varchar(255)
);",
                "Basic type conversion, back ticks and ignoring all after brackets"
            ),
            array(
                "--
-- Database: `fishdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `Blog` (
  `id` int(11), -- comments can occur anywhere
  `name` varchar(255) /* Different
style */
) ;",
                "--
-- Database: `fishdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE 'Blog' (
  'id' int(11), -- comments can occur anywhere
  'name' varchar(255) /* Different
style */
) ;",
                "Both types of comment are fine"
            ),
            array(
                "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";

CREATE TABLE `Blog` (
  `id` int(11)
);",
                "CREATE TABLE `Blog` (
  `id` int(11)
);",
                "Set SQL_mode removed"
            ),
            array(
                "CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;",
                "CREATE TABLE 'blog' (
  'id' int(11) NOT NULL PRIMARY KEY AUTOINCREMENT,
  'name' varchar(255) DEFAULT NULL
);",
                "If auto-increment, then remove primary key statement and ignore start value"
            ),
            array(
                "CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11),
  `name` varchar(255)
);",
                "CREATE TABLE IF NOT EXISTS 'blog' (
  'id' int(11),
  'name' varchar(255)
);",
                "'If not exists' is allowed"
            ),
            array(
                "CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11),
  `name` varchar(255),
  CONSTRAINT PRIMARY KEY (`id`, `name`)
);",
                "CREATE TABLE IF NOT EXISTS 'blog' (
  'id' int(11),
  'name' varchar(255),
  CONSTRAINT PRIMARY KEY (id, name)
);",
                "If unique key is not specified per column, can be given as a constraint"
            ),
        );
    }
} 
