(function($) {
  'use strict';
var dataSet = [
    [ "Tiger Nixon", "System Architect", "B.Tech", "5421789", "2011/04/25", "$320,800" ],
    [ "Garrett Winters", "Accountant", "M.com", "8422895", "2011/07/25", "$170,750" ],
    [ "Ashton Cox", "Junior Technical Author", "B.Tech(Ec)", "15627840", "2009/01/12", "$86,000" ],
    [ "Cedric Kelly", "Senior Javascript Developer", "MCA", "6224899", "2012/03/29", "$433,060" ],
    [ "Airi Satou", "Accountant", "M.com", "5407890", "2008/11/28", "$162,700" ],
    [ "Brielle Williamson", "Integration Specialist", "B.Arch", "4804904", "2012/12/02", "$372,000" ],
    [ "Herrod Chandler", "Sales Assistant", "MBA", "96088940", "2012/08/06", "$137,500" ],
    [ "Rhona Davidson", "Integration Specialist", "B.Arch", "6200940", "2010/10/14", "$327,900" ],
    [ "Colleen Hurst", "Javascript Developer", "MCA", "2360980", "2009/09/15", "$205,500" ],
    [ "Sonya Frost", "Software Engineer", "Computer", "1667890", "2008/12/13", "$103,600" ],
    [ "Jena Gaines", "Office Manager", "MBA", "3814930", "2008/12/19", "$90,560" ],
    [ "Quinn Flynn", "Support Lead", "MBA", "9497890", "2013/03/03", "$342,000" ],
    [ "Charde Marshall", "Regional Director", "Phd", "67419300", "2008/10/16", "$470,600" ],
    [ "Haley Kennedy", "Senior Marketing Designer", "Mba", "35979404", "2012/12/18", "$313,500" ],
    [ "Tatyana Fitzpatrick", "Regional Director", "Phd", "1965890", "2010/03/17", "$385,750" ],
    [ "Michael Silva", "Marketing Designer", "MBA", "1581738", "2012/11/27", "$198,500" ],
    [ "Paul Byrd", "Chief Financial Officer (CFO)", "B.A", "3059930", "2010/06/09", "$725,000" ],
    [ "Gloria Little", "Systems Administrator", "M.com", "17210808", "2009/04/10", "$237,500" ],
    [ "Bradley Greer", "Software Engineer", "MCA", "25580000", "2012/10/13", "$132,000" ],
    [ "Dai Rios", "Personnel Lead", "M.A", "229066767", "2012/09/26", "$217,500" ],
    [ "Haley Kennedy", "Senior Marketing Designer", "Mba", "35979404", "2012/12/18", "$313,500" ],
    [ "Tatyana Fitzpatrick", "Regional Director", "Phd", "1965890", "2010/03/17", "$385,750" ],
    [ "Michael Silva", "Marketing Designer", "MBA", "1581738", "2012/11/27", "$198,500" ],
    [ "Paul Byrd", "Chief Financial Officer (CFO)", "B.A", "3059930", "2010/06/09", "$725,000" ],
    [ "Gloria Little", "Systems Administrator", "M.com", "17210808", "2009/04/10", "$237,500" ],
    [ "Bradley Greer", "Software Engineer", "MCA", "25580000", "2012/10/13", "$132,000" ],
    [ "Dai Rios", "Personnel Lead", "M.A", "229066767", "2012/09/26", "$217,500" ],
    
  ];
var dataSet2 = [
    [ "Tiger Nixon", "1", "B.Tech", "5421789", "2011/04/25", "kr@gmail.com" ],
    [ "Garrett Winters", "2", "M.com", "8422895", "2011/07/25", "gr@gmail.com" ],
    [ "Ashton Cox", "3", "B.Tech(Ec)", "15627840", "2009/01/12", "k456@gmail.com" ],
    [ "Cedric Kelly", "4", "MCA", "6224899", "2012/03/29", "kr@gmail.com" ],
    [ "Airi Satou", "5", "M.com", "5407890", "2008/11/28", "gr@gmail.com" ],
    [ "Brielle Williamson", "6", "B.Arch", "4804904", "2012/12/02", "gr786@gmail.com" ],
    [ "Herrod Chandler", "7", "MBA", "96088940", "2012/08/06", "gr@gmail.com" ],
    [ "Rhona Davidson", "8", "B.Arch", "6200940", "2010/10/14", "k456@gmail.com" ],
    [ "Colleen Hurst", "9", "MCA", "2360980", "2009/09/15", "k453@gmail.com" ],
    [ "Sonya Frost", "10", "Computer", "1667890", "2008/12/13", "jn234@gmail.com" ],
    [ "Jena Gaines", "11", "MBA", "3814930", "2008/12/19",  "jn234@gmail.com" ],
    [ "Quinn Flynn", "12", "MBA", "9497890", "2013/03/03",  "jn234@gmail.com" ],
    [ "Charde Marshall", "13", "Phd", "67419300", "2008/10/16", "470453@gmail.com" ],
    [ "Haley Kennedy", "14", "Mba", "35979404", "2012/12/18", "313@gmail.com" ],
    [ "Tatyana Fitzpatrick", "15", "Phd", "1965890", "2010/03/17", "385@gmail.com" ],
    [ "Michael Silva", "16", "MBA", "1581738", "2012/11/27", "198@gmail.com" ],
    [ "Paul Byrd", "17", "B.A", "3059930", "2010/06/09", "725@gmail.com" ],
    [ "Gloria Little", "18", "M.com", "17210808", "2009/04/10", "gl@gmail.com" ],
    [ "Bradley Greer", "19", "MCA", "25580000", "2012/10/13", "$Da@gmail.com" ],
    [ "Dai Rios", "20", "M.A", "229066767", "2012/09/26", "Da@gmail.com" ],
    [ "Haley Kennedy", "21", "Mba", "35979404","2010/03/17", "Ha@gmail.com "],
    [ "Tatyana Fitzpatrick", "22", "Phd", "1965890", "2010/03/17", "Ta@gmail.com" ],
    [ "Michael Silva", "23", "MBA", "1581738", "2012/11/27", "Mi@gmail.com" ],
    [ "Paul Byrd", "24", "B.A", "3059930", "2010/06/09", "pa@gmail.com" ],
    [ "Gloria Little", "25", "M.com", "17210808", "2009/04/10", "gl@gmail.com" ],
    [ "Bradley Greer", "26", "MCA", "25580000", "2012/10/13", "br@gmail.com" ],
    [ "Dai Rios", "27", "M.A", "229066767", "2012/09/26", "Da@gmail.com" ],
    
  ];

var dataSet3 = [
  
    [ "Tiger Nixon", "System Architect", "B.Tech", "5421789", "2011/04/25", "$320,800" ],
    [ "Garrett Winters", "Accountant", "M.com", "8422895", "2011/07/25", "$170,750" ],
    [ "Ashton Cox", "Junior Technical Author", "B.Tech(Ec)", "15627840", "2009/01/12", "$86,000" ],
    [ "Cedric Kelly", "Senior Javascript Developer", "MCA", "6224899", "2012/03/29", "$433,060" ],
    [ "Airi Satou", "Accountant", "M.com", "5407890", "2008/11/28", "$162,700" ],
    [ "Brielle Williamson", "Integration Specialist", "B.Arch", "4804904", "2012/12/02", "$372,000" ],
    [ "Herrod Chandler", "Sales Assistant", "MBA", "96088940", "2012/08/06", "$137,500" ],
    [ "Rhona Davidson", "Integration Specialist", "B.Arch", "6200940", "2010/10/14", "$327,900" ],
    [ "Colleen Hurst", "Javascript Developer", "MCA", "2360980", "2009/09/15", "$205,500" ],
    [ "Sonya Frost", "Software Engineer", "Computer", "1667890", "2008/12/13", "$103,600" ],
    [ "Jena Gaines", "Office Manager", "MBA", "3814930", "2008/12/19", "$90,560" ],
    [ "Quinn Flynn", "Support Lead", "MBA", "9497890", "2013/03/03", "$342,000" ],
    [ "Charde Marshall", "Regional Director", "Phd", "67419300", "2008/10/16", "$470,600" ],
    [ "Haley Kennedy", "Senior Marketing Designer", "Mba", "35979404", "2012/12/18", "$313,500" ],
    [ "Tatyana Fitzpatrick", "Regional Director", "Phd", "1965890", "2010/03/17", "$385,750" ],
    [ "Michael Silva", "Marketing Designer", "MBA", "1581738", "2012/11/27", "$198,500" ],
    [ "Paul Byrd", "Chief Financial Officer (CFO)", "B.A", "3059930", "2010/06/09", "$725,000" ],
    [ "Gloria Little", "Systems Administrator", "M.com", "17210808", "2009/04/10", "$237,500" ],
    [ "Bradley Greer", "Software Engineer", "MCA", "25580000", "2012/10/13", "$132,000" ],
    [ "Dai Rios", "Personnel Lead", "M.A", "229066767", "2012/09/26", "$217,500" ],
    [ "Haley Kennedy", "Senior Marketing Designer", "Mba", "35979404", "2012/12/18", "$313,500" ],
    [ "Tatyana Fitzpatrick", "Regional Director", "Phd", "1965890", "2010/03/17", "$385,750" ],
    [ "Michael Silva", "Marketing Designer", "MBA", "1581738", "2012/11/27", "$198,500" ],
    [ "Paul Byrd", "Chief Financial Officer (CFO)", "B.A", "3059930", "2010/06/09", "$725,000" ],
    [ "Gloria Little", "Systems Administrator", "M.com", "17210808", "2009/04/10", "$237,500" ],
    [ "Bradley Greer", "Software Engineer", "MCA", "25580000", "2012/10/13", "$132,000" ],
    [ "Dai Rios", "Personnel Lead", "M.A", "229066767", "2012/09/26", "$217,500" ],
    
  ];
    
    var dataSet4 = [
    [ "Tiger Nixon", "1", "B.Tech", "5421789", "2011/04/25", "$135" ],
    [ "Garrett Winters", "2", "M.com", "8422895", "2011/07/25", "$145" ],
    [ "Ashton Cox", "3", "B.Tech(Ec)", "15627840", "2009/01/12", "$155" ],
    [ "Cedric Kelly", "4", "MCA", "6224899", "2012/03/29", "$135" ],
    [ "Airi Satou", "5", "M.com", "5407890", "2008/11/28", "$165" ],
    [ "Brielle Williamson", "6", "B.Arch", "4804904", "2012/12/02", "$195" ],
    [ "Herrod Chandler", "7", "MBA", "96088940", "2012/08/06", "$235" ],
    [ "Rhona Davidson", "8", "B.Arch", "6200940", "2010/10/14", "$435" ],
    [ "Colleen Hurst", "9", "MCA", "2360980", "2009/09/15", "$135"],
    [ "Sonya Frost", "10", "Computer", "1667890", "2008/12/13", "$735" ],
    [ "Jena Gaines", "11", "MBA", "3814930", "2008/12/19",  "$135"],
    [ "Quinn Flynn", "12", "MBA", "9497890", "2013/03/03",  "$535" ],
    [ "Charde Marshall", "13", "Phd", "67419300", "2008/10/16", "$185" ],
    [ "Haley Kennedy", "14", "Mba", "35979404", "2012/12/18", "$195"],
    [ "Tatyana Fitzpatrick", "15", "Phd", "1965890", "2010/03/17", "$135" ],
    [ "Michael Silva", "16", "MBA", "1581738", "2012/11/27", "$1035" ],
    [ "Paul Byrd", "17", "B.A", "3059930", "2010/06/09", "$725" ],
    [ "Gloria Little", "18", "M.com", "17210808", "2009/04/10", "$435" ],
    [ "Bradley Greer", "19", "MCA", "25580000", "2012/10/13", "$325" ],
    
  ];
    
  var dataSet5 = [
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    [ "Meachnical Engg", "Kalvine", "7829828", "kr@gmail.com", "1998", "135" ],
     [ "Computer", "Kareena", "7829828", "jr@gmail.com", "2000", "145" ],
    [ "MCA", "Lorvine", "7829828", "Lr@gmail.com", "1997", "195" ],
    
  ];
    

    
    
    
     var dataSet6 = [
    [ "A", "1", "Medium", "2", "2", "20$" ],
     [ "B", "2", "small", "6", "4", "10$" ],
    [ "C", "3", "Large", "12", "10", "15$" ],
    [ "D", "23", "Medium", "3", "2", "5$" ],
     [ "E", "45", "Large", "4", "3", "20$" ],
    [ "F", "36", "small", "2", "2", "10$" ],
    [ "A", "1", "Medium", "2", "2", "20$" ],
     [ "B", "2", "small", "6", "4", "10$" ],
    [ "C", "3", "Large", "12", "10", "15$" ],
    [ "D", "23", "Medium", "3", "2", "5$" ],
     [ "E", "45", "Large", "4", "3", "20$" ],
    [ "F", "36", "small", "2", "2", "10$" ],
    [ "A", "1", "Medium", "2", "2", "20$" ],
     [ "B", "2", "small", "6", "4", "10$" ],
    [ "C", "3", "Large", "12", "10", "15$" ],
    [ "D", "23", "Medium", "3", "2", "5$" ],
     [ "E", "45", "Large", "4", "3", "20$" ],
    [ "F", "36", "small", "2", "2", "10$" ],
    [ "A", "1", "Medium", "2", "2", "20$" ],
     [ "B", "2", "small", "6", "4", "10$" ],
    [ "C", "3", "Large", "12", "10", "15$" ],
    [ "D", "23", "Medium", "3", "2", "5$" ],
     [ "E", "45", "Large", "4", "3", "20$" ],
    [ "F", "36", "small", "2", "2", "10$" ],
    
  ];
    
    
 var dataSet7 = [
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
    [ "john", "1234567", "VX-235", "VX-234", "Brokylen to botanic", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "john Lary", "2234567", "DX-235", "CX-234", "Brokylen to Newyork", "<i class='fas fa-map-marked-alt'></i>" ],
     [ "Korey", "13456745", "KX-435", "TX-234", "Brokylen to Washingaton", "<i class='fas fa-map-marked-alt'></i>" ],
     
  ];
    
   
     var dataSet8 = [
    [ "Lorvil", "20", "Cricket", "Male", "State", "2" ],
     [ "Kery", "23", "Volyball", "Female", "District", "1" ],
     [ "John", "25", "basketball", "Male", "National", "4" ],
     [ "Lucy", "20", "Rifle Shooting", "Female", "international", "4" ],
     [ "Elvine", "24", "Gymanstic", "Male", "National", "6" ],
    [ "Lorvil", "20", "Cricket", "Male", "State", "2" ],
     [ "Kery", "23", "Volyball", "Female", "District", "1" ],
     [ "John", "25", "basketball", "Male", "National", "4" ],
     [ "Lucy", "20", "Rifle Shooting", "Female", "international", "4" ],
     [ "Elvine", "24", "Gymanstic", "Male", "National", "6" ],
 [ "Lorvil", "20", "Cricket", "Male", "State", "2" ],
     [ "Kery", "23", "Volyball", "Female", "District", "1" ],
     [ "John", "25", "basketball", "Male", "National", "4" ],
     [ "Lucy", "20", "Rifle Shooting", "Female", "international", "4" ],
     [ "Elvine", "24", "Gymanstic", "Male", "National", "6" ],
    
  ];     
    
 var dataSet9 = [
    [ "1", "New Year's Day", "Public Holiday", "01 January 2021", "03 January 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "2", "Memorial Day", "Public Holiday", "29 May 2021", "29 May 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "3", "Christmas Day", "Public Holiday", "25 December 2021", "03 January 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "4", "Holiday By Collage", "Public Holiday", "03 March 2021", "09-March 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "5", "New Year's Day", "Public Holiday", "01 January 2021", "03 January 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "6", "Memorial Day", "Public Holiday", "29 May 2021", "29 May 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "7", "Christmas Day", "Public Holiday", "25 December 2021", "03 January 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "8", "Holiday By Collage", "Public Holiday", "03 March 2021", "09-March 2021", "Lorem Ipsum is simply dummy text of the printing" ],
    [ "9", "New Year's Day", "Public Holiday", "01 January 2021", "03 January 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "10", "Memorial Day", "Public Holiday", "29 May 2021", "29 May 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "11", "Christmas Day", "Public Holiday", "25 December 2021", "03 January 2021", "Lorem Ipsum is simply dummy text of the printing" ],
     [ "12", "Holiday By Collage", "Public Holiday", "03 March 2021", "09-March 2021", "Lorem Ipsum is simply dummy text of the printing" ],
  ];     
        
    
    
  var tableOne = $('#data-table-1').DataTable( {
    data: dataSet,
    columns: [
      { title: "Name" },
      { title: "Departement" },
      { title: "Degree" },
      { title: "Mobile" },
      { title: "Joining date" },
      { title: "Salary" }
    ],
  });
  var tableTwo = $('#data-table-2').DataTable( {
    data: dataSet4,
    columns: [
      { title: "Name" },
      { title: "Roll No" },
      { title: "Class" },
      { title: "Mobile No" },
      { title: "Fees date" },
      { title: "Fees" }
    ],
  });
  var tableThree = $('#data-table-3').DataTable( {
    data: dataSet3,
    columns: [
      { title: "Name" },
      { title: "Desgination" },
      { title: "Degree" },
      { title: "Mobile" },
      { title: "Joining date" },
      { title: "Salary" }
    ],
    scrollY: 400
  });
  var tableFour = $('#data-table-4').DataTable( {
    data: dataSet2,
    columns: [
      { title: "Name" },
      { title: "Roll No" },
      { title: "Class" },
      { title: "Mobile" },
      { title: "Admission date" },
      { title: "Email" }
    ],
  });
    
   var tableOne = $('#data-table-5').DataTable( {
    data: dataSet5,
    columns: [
      { title: "Departement Name" },
      { title: "Head of Dept" },
      { title: "Phone" },
      { title: "Email" },
      { title: "Starting Year" },
      { title: "Student Capacity" }
    ],
  });  
    
    var tableOne = $('#data-table-6').DataTable( {
    data: dataSet6,
    columns: [
      { title: "Block" },
      { title: "Room No" },
      { title: "Type" },
      { title: "Number Of Bed" },
      { title: "Avalibality" },
      { title: "Cost Per Bed" }
    ],
  });  
    
     var tableOne = $('#data-table-7').DataTable( {
    data: dataSet7,
    columns: [
      { title: "Driver Name" },
      { title: "Mobile Number" },
      { title: "Licence Number" },
      { title: "Vehicle Number" },
      { title: "Route Name" },
      { title: "MAP" }
    ],
  });
     var tableOne = $('#data-table-8').DataTable( {
    data: dataSet8,
    columns: [
      { title: "Name" },
      { title: "Age" },
      { title: "Sport Name" },
      { title: "Gender" },
      { title: "Sport Level" },
      { title: "Wins Medal" }
    ],
  });    
    
    var tableOne = $('#data-table-9').DataTable( {
    data: dataSet9,
    columns: [
      { title: "#" },
      { title: "Title" },
      { title: "Type" },
      { title: "Start-Date" },
      { title: "End-date" },
      { title: "Details" }
    ],
  });    
    
    
})(jQuery);
