@extends('layouts.dashboard_template')

@section('heading')
	Performance
@endsection


@section('content')

	<div class="chart-wrapper">
		<div class="chart-container">
			<canvas id="bar-chart"></canvas>
		</div>
		<div class="chart-text">
			Time Spent In the Week: {{ $total }} Hours 
		</div>
	</div>
	<script>
		
		// var data = [ {{$data[6]}},{{$data[5]}},{{$data[4]}},{{$data[3]}},{{$data[2]}},{{$data[1]}},{{$data[0]}}];	
		// var date = [ "{{$date[6]}}","{{$date[5]}}","{{$date[4]}}","{{$date[3]}}","{{$date[2]}}","{{$date[1]}}","{{$date[0]}}"];
		var data = []
		@for ($i = 19; $i >= 0; $i--)
			data.push({{$data[$i]}});
		@endfor
		var date = []
		@for ($i = 19; $i >= 0; $i--)
			date.push("{{$date[$i]}}");
		@endfor
		var colors = []
		var colors_list = ['#FFB1C1', '#FFCF9F', '#4BC0C0', '#9AD0F5'];
		
		@for ($i = 19; $i >= 0; $i--)
			colors.push(colors_list[Math.floor(Math.random()*4)]);
		@endfor
		// console.log(colors);

		var ctx = document.getElementById("bar-chart");
		var myChart = new Chart(ctx, {
						    type: 'bar',
						    data: {
						        labels: date,
						        datasets: [{
						            label: 'Hours Active',
						            data: data,
						            backgroundColor: colors ,
						            borderColor: colors,
						            borderWidth: 1
						        }]
						    },
						    options: {
						        scales: {
						            yAxes: [{
						                ticks: {
						                    beginAtZero:true
						                },
						                scaleLabel: {
						                	display: true,
						                	labelString: 'Hours'
						                }
						            }]
						        }
						    }
						});

	</script>
@endsection
