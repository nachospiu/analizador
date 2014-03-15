<?php

// base class with member properties and methods
class Vegetable {

   var $edible;
   var $color;

   function Vegetable($edible, $color="green") 
   {
       $this->edible = $edible;
       $this->color = $color;
   }

   function is_edible() 
   {
		$hola = 'hola';
       return $this->edible;
   }

   function what_color() 
   {
       return $this->color;
   }
   
} // end of class Vegetable

// extends the base class
class Spinach extends Vegetable {

   var $cooked = false;

   function Spinach() 
   {
       $this->Vegetable(true, "green");
       
       funccion($variableNoDefinida);
       
       if(true) {}
   }

   function cook_it() 
   {
   		var_dump($color);
   	
       $this->cooked = true;
   }

   function is_cooked() 
   {
   		var_dump($uno);
   	
       return $this->cooked;
   }
   
} // end of class Spinach

?>