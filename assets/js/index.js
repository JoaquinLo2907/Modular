(function($) {
  'use strict';

  // Bar chart
  var barChart = new Chart(document.getElementById("bar-chart"), {
      type: 'bar',
      data: {
        labels: ["Chemistery", "Mechanical", "Electrical", "Civil","Computer"],
        datasets: [
          {
            label: "Universites (departement results)",
            backgroundColor: ["#000000", "#fbb022","#000000","#fbb022","#000000"],
            data: [2009,2014,2013,2015,2020]
          }
        ]
      },
      options: {
        legend: { display: false },
        title: {
          display: true,
          text: 'Year 2021 Universties results(Departements)'
        }
      }
  });
    })(jQuery);