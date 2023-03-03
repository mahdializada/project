<template>
	<v-simple-table dense>
		<template v-slot:default>
			<thead>
				<tr style="background-color: #cedcf9">
					<th v-for="(head, n) in tableHeaders" :key="n" class="text-left text-capitalize primary--text">
						{{ head }}
					</th>					
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, row) in tableItems" :key="row">                   
                    <td v-for="(value, key) in item" :key="key" > {{ value }} </td>
				</tr>
			</tbody>
		</template>
	</v-simple-table>
</template>


<script>
export default {
    name: 'CustomSimpleTable',
    props: ['tableHeaders', 'tableItems'],
}
</script>

<style scoped>
	.v-data-table table tbody tr:nth-of-type(even):not(.v-data-table__selected):hover{
		cursor: default;
  		background-color: #fff !important;
		color: black;
	}
	.v-data-table table tbody tr:not(.v-data-table__selected):hover{
		cursor: default;
  		background-color: #f1f2f2 !important;
		color: black;
	}
</style>