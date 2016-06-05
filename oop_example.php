<?php
class Book {
    //properties
    private $title;
    private $author;

    //------------------------------------------------------------

    public function __construct($newTitle, $newAuthor) {
     $this->title = $newTitle;
        $this->author = $newAuthor;
    }

    public function setTitle($newTitle) {
     $this->title = $newTitle;
    }

    public function setAuthor($newAuthor) {
     $this->author = $newAuthor;
    }

    public function getTitle() {
     return $this->title;
    }

    public function getAuthor() {
     return $this->author;
    }

} //end of class.  The above code would normally be stored in a separate php file

//testing code

//first create 2 new books
$mybook1 = new Book('OOP is Great','Fred Smith');
$mybook2 = new Book('I like OOP','Sam Jones');

//now display the current values stored in each book
echo 'Book 1 title = '.$mybook1->getTitle().'<br />';
echo 'Book 1 author = '.$mybook1->getAuthor().'<br />';
echo 'Book 2 title = '.$mybook2->getTitle().'<br />';
echo 'Book 2 author = '.$mybook2->getAuthor().'<br /><br />';

//now change some values in each book
$mybook1->setTitle('OOP is Great - 2nd Edition');
$mybook2->setAuthor('John Smith');

//now display the current values stored in each book
echo 'Book 1 title = '.$mybook1->getTitle().'<br />';
echo 'Book 1 author = '.$mybook1->getAuthor().'<br />';
echo 'Book 2 title = '.$mybook2->getTitle().'<br />';
echo 'Book 2 author = '.$mybook2->getAuthor().'<br /><br />';
?>
