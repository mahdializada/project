import Vue from "vue";
import { Line } from "vue-chartjs/legacy";
import {
	Chart as ChartJS,
	Title,
	Tooltip,
	Legend,
	BarElement,
	CategoryScale,
	LinearScale,
	LineElement,
	PointElement,
} from "chart.js";
import htmlLegendPlugin from "./chartjs/htmlLegend";

ChartJS.register(
	Title,
	Tooltip,
	Legend,
	PointElement,
	BarElement,
	CategoryScale,
	LinearScale,
	LineElement,
	htmlLegendPlugin
);

Vue.component("line-chart", {
	extends: Line,
});
