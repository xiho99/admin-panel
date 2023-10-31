<template>
	<div class="table-demo-container layout-padding">
		<div class="table-demo-padding layout-padding-view layout-padding-auto">
			<div style="display: flex; justify-content: space-between;" class="mr10">
				<div style="flex:1;">
					<TableSearch :search="state.search" @search="getTableData" />
				</div>
				<el-button size="default" type="primary" class="mt10" @click="onReset(tableSearchRef)"> 新增 </el-button>
			</div>
			<div style="padding: 20px;display:flex;flex-wrap: wrap;">
				<el-tag v-for="item in state.tableData" style="margin-right: 10px;">{{item.name}}</el-tag>
				<el-tag class="mx-1" size="large" v-show="state.addTag"><el-input v-model="state.fromData.name" ref="tegInput" style="height: 30px;" @blur="blurInp"></el-input></el-tag>
				<el-button style="height: 30px;" type="primary" v-if="!state.addTag" @click="toAddTag">新增</el-button>
				<div v-else>
					<el-button style="height: 30px;" @click="saveInfo" :loading="state.loading">确认</el-button>
					<el-button style="height: 30px;" type="info" @click="state.addTag = false" :loading="state.loading">取消</el-button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts" name="/kw">
	import { defineAsyncComponent, reactive, onMounted, ref } from 'vue';
	import { ElMessageBox, ElMessage } from 'element-plus';
	import { getTagList,deleteTag,saveTag } from '/@/api/api';

	// 引入组件
	const Table = defineAsyncComponent(() => import('/@/components/table/index.vue'));
	const TableSearch = defineAsyncComponent(() => import('/@/components/search.vue'));

	// 定义变量内容
	const roleDialogRef = ref();
	const state = reactive({
		addTag:false,
		loading:false,
		tableData: [],
		fromData:{
			name:'',
		},
		search:[
			{ label: '名称', prop: 'name', placeholder: '请输入名称', type: 'input' }
		]
	});
	// 初始化表格数据
	const getTableData = async (query=null) => {
		state.loading = true;
		let row = await getTagList(query);
		state.tableData = row.data;
		state.loading = false;
	};
	const tegInput = ref();
	const toAddTag = async () => {
		state.addTag = true;
		tegInput.value.focus();
	}
	let t1 = null;
	const blurInp = () => {
		t1 = setTimeout(() => {
			state.addTag = false;
		},350)
	}
	const saveInfo = async (e = {}) => {
		clearTimeout(t1);
		state.loading = true;
		try{
			await saveTag({...e,...state.tableData.page});
			state.addTag = false;
			state.fromData.name = '';
			getTableData();
		}catch(e){
			console.log(e,'saveTag')
			//TODO handle the exception
		}
		state.loading = false;
		state.addTag = false;
	}
	// 打开新增角色弹窗
	const onOpenAddRole = () => {
		roleDialogRef.value.openDialog();
	};
	// 打开修改角色弹窗
	const onOpenEditRole = ( row : Object) => {
		roleDialogRef.value.openDialog('edit', row);
	};
	const deleteRow = (row:object) => {
		ElMessageBox.confirm(`此操作将永久删除，是否继续?`, '提示', {
			confirmButtonText: '确认',
			cancelButtonText: '取消',
			type: 'warning',
		})
			.then(async () => {
				await deleteTag({id:row.id});
				getTableData();
				ElMessage.success('删除成功');
			})
			.catch(() => { });
	}
	// 删除角色
	const onSystem = (row :object,key:string) => {
		let h = {
			edit:onOpenEditRole,
			delete:deleteRow,
		}
		if(h[key]) h[key](row);
	};
	// 分页改变
	const onHandlePageChange = (data :object) => {
		state.tableData.page.pageSize = data.pageSize;
		state.tableData.page.page = data.page;
		getTableData();
	};
	// 页面加载时
	onMounted(() => {
		getTableData();
	});
</script>

<style scoped lang="scss">
	.system-role-container {
		.system-role-padding {
			padding: 15px;

			.el-table {
				flex: 1;
			}
		}
	}
</style>