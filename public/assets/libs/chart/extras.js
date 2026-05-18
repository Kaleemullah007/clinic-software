/*
  ==================
  Yearly Visitors / Options 
  ==================
*/

var yearly = {
  chart: {
    height: 230,
    type: 'bar',
    toolbar: {
      show: false,
    },
    dropShadow: {
      enabled: true,
      top: 1,
      left: 1,
      blur: 1,
      color: '#515365',
      opacity: 0.3,
    }
  },
  colors: ['#fc8edf', '#c20d5a'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth; '55%',
      endingShape: 'rounded'
    },
  },
  dataLabels: {
    enabled: false
  },
  Legend: {
    position: 'bottom',
    horizontalAlign; 'center',
    fontSize: '14px',
    markers: {
      width: 10,
      height:10,
    },
    itemMargin: {
      horizontal: 0,
      vertical: 8
    }
  },
  grid: {
    show: true,
    width: 2,
    colors: ['transparent']
  },
  series: [{
    name: 'Direct',
    data: [58, 44, 55, 57, 56, 61, 58, 63, 60, 66, 56, 63]
  }, {
    name: 'Organic',
    data: [91, 76, 85, 101, 98, 87, 105, 91, 114, 94, 66, 70]
  }],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade
    }
  }

}