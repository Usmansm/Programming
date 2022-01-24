var ChartManager, root,
  bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };
 ChartManager = (function() {
 function ChartManager(config,panelUIConfig,data) {
		   var scope = this;
		   scope._config = config;
		   scope._data = data;
		   scope.panelUIConfig = panelUIConfig;
	  }
	 return ChartManager;
 })();
 
 ChartManager.prototype.drawChart = function(){
	  var scope = this ;
	  var config = scope._config;
	  scope.tooltip = CustomTooltip("gates_tooltip", 240);
	  this.createSvgPanel();
	  this.createXAxis();
	  this.createYAxis();
	  scope.drawSeries();
	  
 };
 ChartManager.prototype.drawSeries = function(){
		var me = this ;
		var config = me._config;
		var seriesType = config.seriesType;
		switch(seriesType){
			case "column":
			 me.drawColumnSeries();
			break;
		}
		
 }
 ChartManager.prototype.drawColumnSeries = function(){
	 var me = this ;
	 var panelUIConfig = me.panelUIConfig;
	 var width = panelUIConfig.svgWidth;
	 var height = panelUIConfig.svgHeight;
	 var data  = me._data;config = me._config;
	 var svg = this.svg,x = this._x,y = this._y;
	  var yDim = config.selectedYDim,xDim = config.selectedXDim;
	  var seriesColor = config.seriesColor;
	 svg.selectAll("bar")
      .data(data)
	 .enter().append("rect")
      .style("fill", seriesColor)
      .attr("x", function(d) { return x(d[xDim]); })
      .attr("width", x.rangeBand())
      .attr("y", function(d) { return y(d[yDim]); })
      .attr("height", function(d) { return height - y(d[yDim]); })
	  .on("mouseover", function(d, i) {
		  if(config.toolTips["enable"])
		  {
			return me.showDetails(d, i, this);  
		  }
	  }).on("mouseout", function(d, i) {
		  if(config.toolTips["enable"])
		  {
			return me.hideDetails(d, i, this);
		  }
	  })
 }
  ChartManager.prototype.hideDetails = function(data, i, element){
	  return this.tooltip.hideTooltip();
};
ChartManager.prototype.showDetails =function(data, i, element)
{
	 var me = this ;
	 var content,tipsMsg;
	 var config = me._config;
	d3.select(element).attr("stroke", "white");
	tipsMsg = me.getToolTipsMsg(data, i);
	return this.tooltip.showTooltip(tipsMsg, d3.event,element);
	   
 }
ChartManager.prototype.getToolTipsMsg =function(data, i)
{
	var msg = "";
	  msg = msg+"<span class=\"name\">"+"Month"+" : </span>"+"<span class=\"value\"> " + data["monthYrDisplay"] + "</span><br/>"
	msg = msg+"<span class=\"name\">"+"Count"+" : </span>"+"<span class=\"value\"> " + data["count"] + "</span><br/>"
	 
	return msg; 
}
 	 
 ChartManager.prototype.getYMinMax = function(yAxy){
		var me = this ;
	    var data  = me.formatedData;
	    var yData = [];
		var data = me._data;
		for(var i=0;i< data.length ; i++){
			var edata = data[i];
			yData.push(edata[yAxy]);
		}
		yData = yData.sort();
		var minYdata = yData.min(),maxYData= yData.max();
		return {
			yMin:0,
			yMax:maxYData
		}
 }
 ChartManager.prototype.createYAxis = function()
 {
	  var me = this ;
	  var svg = me.svg ,config = me._config;
	  var yAxisConfig = config["yAxis"];
	  var panelUIConfig = me.panelUIConfig;
	  var width = panelUIConfig.svgWidth;
	  var height = panelUIConfig.svgHeight;
	  var yDim = config.selectedYDim;
	  var y = d3.scale.linear()
			.range([height, 0],0.5);
	  var yValue = function(d) { return d[yDim];};
	  var yAxis = d3.svg.axis().scale(y).orient("left").
				tickFormat(function (d) {
					var prefix = d3.formatPrefix(d);
					return prefix.scale(d) + prefix.symbol;
			});
	 var yConfig = me.getYMinMax(yDim);
	 console.log(yConfig);
     y.domain([yConfig.yMin,yConfig.yMax])
	 me._yValue = yValue;
	 var currentYaxis = svg.append("g")
	  .attr("class", "y axis")
	  .call(yAxis);
	  
	//Axis Title Label
	 if(yAxisConfig["showYlabel"])
		{
			 var yTitle = yAxisConfig["yTitle"]
			 currentYaxis.append("text")
			  .attr("class", "label")
			  .attr("transform", "rotate(-90)")
			  .attr("y", 6)
			  .attr("x", -6)
			  .attr("dy", ".71em")
			  .style("text-anchor", "end")
			  .text(yTitle);
		}
		this._yAxis = yAxis;
		console.log(y)
		this._y = y;
		return yAxis;
 }
 ChartManager.prototype.createXAxis = function()
 {
	var me = this ,svg =me.svg;
	var panelUIConfig = me.panelUIConfig;
	var width = panelUIConfig.svgWidth,height = panelUIConfig.svgHeight;
	var data = me._data;
	var selectedXDim = me._config["selectedXDim"];
	console.log("selectedXDim ",selectedXDim,data);
	var xData = [];
	console.log(data.length);
	for(var i =0 ;i < data.length ;i++){
		var edata = data[i];
		xData.push(edata["monthYrDisplay"]);
	}
	
	var xdomain = d3.scale.ordinal()
	.domain(xData)
	.rangeRoundBands([0, width], .05)
	
	var xAxis = d3.svg.axis()
		.scale(xdomain)
		.orient("bottom").ticks(10);
		
	   
	 var currXaxis = svg.append("g")
			  .attr("class", "x axis")
			  .attr("transform", "translate(0," + height + ")")
			  .call(xAxis);
	  	
		currXaxis.selectAll("text")
		.attr("y", 0)
		.attr("x", -6)
		.attr("dy", ".35em")
		.attr("style","text-anchor: start;font-size: 11px;")
		.attr("transform", function(d) {
			return "translate(0,10)";
		})
	    //Axis Tick Label
	    currXaxis.selectAll("text")
		.attr("y", 0)
		.attr("x", -6)
		.attr("dy", ".35em")
		.attr("style","text-anchor: start;font-size: 11px;")
		.attr("transform", function(d) {
			return "translate(0,10)";
		})
		.style("font-size", "11px")
		.style("text-anchor", "start");
	me._x = xdomain;
    
 }
 ChartManager.prototype.createSvgPanel = function(){
	var me = this ;
	var panelUIConfig = me.panelUIConfig;
	var width = panelUIConfig.svgWidth;
	var height = panelUIConfig.svgHeight;
	var chartDiv = panelUIConfig["chartDiv"];
	var margin = panelUIConfig["margin"];
	console.log( d3.select(chartDiv));
	var svg = d3.select("body").append("svg")
	.attr("width", width + margin.left + margin.right)
	.attr("height", height + margin.top + margin.bottom)
	.append("g")
	.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	me.svg = svg;
};
Array.prototype.min = function(){
  return Math.min.apply(null, this);
 }
 Array.prototype.max = function(){
  return Math.max.apply(null, this);
 }
