<?php

Class financialDates { 
    
    var $startDateCurrentMonth;
    var $endDateCurrentMonth;
    
    var $startDatePreviousMonth;
    var $endDatePreviousMonth;
    
    var $currentFinancialPeriod;
    var $currentFinancialYear;
    
    var $startDateFinancialYear;
    var $endDateFinancialYear;
    
    var $startDatePreviousFinancialYear;
    var $endDatePreviousFinancialYear;
    
    var $currentDate;
    
   function __construct($date=null) 
   {
       $this->currentDate = (empty($date)) ? new DateTime() : new DateTime($date);
       # current month
       $this->startDateCurrentMonth = $this->currentDate->format('Y-m-01');
       $this->endDateCurrentMonth = $this->currentDate->format('Y-m-t');
       
       # previous month
       $startMonth = new DateTime($this->startDateCurrentMonth);
       $this->startDatePreviousMonth = $startMonth->modify('-1 year')->format('Y-m-01');
       $this->endDatePreviousMonth = $startMonth->format('Y-m-t');
       
       # current year
       $g = date('Y-m-01',  strtotime((string)$GLOBALS['xmlConfig']->financialYearStartMonth));
       $gregorianFinancialStartDate = new DateTime($g);
       
       while($gregorianFinancialStartDate>$this->currentDate) $gregorianFinancialStartDate->modify('-1 year');
       $this->startDateFinancialYear = $gregorianFinancialStartDate->format('Y-m-d');
       $this->endDateFinancialYear = $gregorianFinancialStartDate->modify('+1 year')->modify('-1 day')->format('Y-m-d');
       
       # financial year
       $this->currentFinancialYear = $gregorianFinancialStartDate->format('Y');
       
       # previous year
       $this->startDatePreviousFinancialYear = $gregorianFinancialStartDate->modify('-2 years')->modify('+1 day')->format('Y-m-d');
       $this->endDatePreviousFinancialYear = $gregorianFinancialStartDate->modify('+1 year')->modify('-1 day')->format('Y-m-d');
       
       # financial period
       $startDate = new DateTime($this->startDateFinancialYear);
       $endDate = new DateTime($this->endDateCurrentMonth);
       $period = 1;
       $m = $startDate->format('m');
       while (empty($this->currentFinancialPeriod))
       {
           $period++;
           $m++;
           if ($m==13) $m = 1;
           if ($period==13) $period = 1;
           if ($endDate->format('m')==$m) $this->currentFinancialPeriod = $period;
       }
   }
    
}