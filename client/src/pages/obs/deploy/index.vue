<template>
	<div class="table-demo-container layout-padding w100">
		<div class="table-demo-padding layout-padding-view layout-padding-auto pt15 pl15 pb15 pr15">
			<el-menu :default-active="state.configuration_group_id" class="el-menu-demo" mode="horizontal"
				@select="handleSelect" background-color="#fff">
				<el-menu-item v-for="(item,index) in state.groupData" :index="item.id + ''">{{item.name}}</el-menu-item>
				<li class="el-menu-item" v-show="state.addGroup"><el-input v-model="state.fromGroupData.name"
						ref="groupInput" style="height: 30px;" @blur="blurInp"></el-input></li>
				<li class="el-menu-item" style="border:1px #ccc solid" v-if="!state.addGroup" @click="toAddTag">
					<el-icon>
						<Plus />
					</el-icon>
				</li>
				<li class="el-menu-item" v-else style="border:1px #ccc solid">
					<el-button style="height: 30px;" @click="saveInfo" :loading="state.loading">确认</el-button>
					<el-button style="height: 30px;" type="info" @click="state.addGroup = false"
						:loading="state.loading">取消</el-button>
				</li>
			</el-menu>
			<el-table class="w100" :data="getNowData">
				<el-table-column label="配置名称" show-overflow-tooltip>
					<template #default="scope">
						<el-input v-if="scope.row.edit" v-model="scope.row.name" placeholder="请输入配置名称"
							clearable></el-input>
						<div v-else>{{scope.row.name}}</div>
					</template>
				</el-table-column>
				<el-table-column label="key" show-overflow-tooltip>
					<template #default="scope">
						<el-input v-if="scope.row.edit" v-model="scope.row.key" placeholder="请输入key值"
							clearable></el-input>
						<div v-else>{{scope.row.key}}</div>
					</template>
				</el-table-column>
				<el-table-column label="输入方式" show-overflow-tooltip>
					<template #default="scope">
						<el-select v-if="scope.row.edit" v-model="scope.row.type" placeholder="请选择内容类型"
							@change="changeType(scope.row)" class="w100">
							<el-option v-for="ite in state.type" :label="ite.label" :value="ite.id"></el-option>
						</el-select>
						<div v-else>{{state.type[scope.row.type]?.label}}</div>
					</template>
				</el-table-column>
				<el-table-column label="内容" show-overflow-tooltip min-width="400">
					<template #default="scope">
						<div v-if="scope.row.edit">
							<el-input v-if="scope.row.type == 1" v-model="scope.row.value" placeholder="请输入内容"
								clearable></el-input>
							<el-input v-else-if="scope.row.type == 2" rows="10" v-model="scope.row.value"
								type="textarea" placeholder="请输入内容" clearable></el-input>
							<el-switch v-else-if="scope.row.type == 3" v-model="scope.row.value" inline-prompt
								active-text="是" inactive-text="否" :active-value="1" :inactive-value="0"></el-switch>
								<Editor v-else-if="scope.row.type == 4" v-model:get-html="scope.row.value"  />
								<el-color-picker v-else-if="scope.row.type == 5" v-model="scope.row.value" size="large" />
								<uploadFile v-else-if="scope.row.type == 6" :limit="9" v-model:get-file-str="scope.row.value" />
						</div>
						<div v-else style="max-height: 200px;">
							<span v-if="scope.row.type ==3">{{['是','否'][scope.row.value]}}</span>
							<div v-if="scope.row.type ==6">
								<img v-for="item in scope.row.value?.split(',')" :src="item" alt="" style="width: 150px;height: auto;">
							</div>
							<span v-else>{{scope.row.value}}</span>
						</div>
					</template>
				</el-table-column>
				<el-table-column label="操作" show-overflow-tooltip min-width="120">
					<template #default="scope">
						<div class="flex-center cursor-pointer" v-if="!scope.row?.edit">
							<el-icon size="25" @click="editForm(scope.row)">
								<Edit />
							</el-icon>
						</div>
						<div v-else>
							<el-button style="height: 30px;" type="primary" @click="savesubInfo(scope.row)"
								:loading="state.loading">确认</el-button>
							<el-button style="height: 30px;" type="info" @click="scope.row.edit = false,getTableData()"
								:loading="state.loading">取消</el-button>
						</div>
					</template>
				</el-table-column>
			</el-table>
			<el-row :gutter="35" class="mt20">
				<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="mb20">
					<el-button type="primary" @click="addDeploy">添 加 配 置</el-button>
				</el-col>
			</el-row>
		</div>
		<!-- el-dialog v-model="state.EditorDialog" width="80%" style="height: 80%;    background: #fff;" show-close>
				<Editor v-model:get-html="scope.row.value"  />
		</el-dialog> -->
	</div>
</template>

<script setup lang="ts" name="/kw">
	import { defineAsyncComponent,computed, reactive, onMounted, ref } from 'vue';
	import { ElMessageBox, ElMessage } from 'element-plus';
	import { saveConfigurationInfo, saveConfigurationGroupInfo, getConfigurationList } from '/@/api/api';
	import { Plus, Edit } from '@element-plus/icons-vue'
	// 引入组件
	const Editor = defineAsyncComponent(() => import('/@/components/editor/index.vue'));
	const uploadFile = defineAsyncComponent(() => import('/@/components/uploadFile/index.vue'));

	const handleSelect = (key : any, keyPath : string[]) => {
		state.configuration_group_id = key + '';
	}
	const state = reactive({
		EditorDialog:false,
		configuration_group_id: null,
		addGroup: false,
		loading: false,
		tableData: [],
		groupData: [],
		fromSubData: [],
		fromGroupData: { name: '' },
		type: { 1: { label: '输入框', id: 1 }, 2: { label: '文本输入框', id: 2 }, 3: { label: '开关', id: 3 }, 4: { label: '富文本编辑器', id: 4 }, 5: { label: '取色器', id: 5 }, 6: { label: '图片上传', id: 6 } },
	});
	const changeType = (item : Object) => {
		item.value = '';
	}
	const addDeploy = () => {
		state.tableData.push({ name: '', configuration_group_id: state.configuration_group_id, key: '', value: '', type: 1, edit: true });
	}
	const editForm =(row: { type: number; value: any; edit: boolean; }) => {
		row.edit = true;
	}
	// 初始化表格数据
	const getTableData = async (query = {}) => {
		state.loading = true;
		let row = await getConfigurationList({...query,...state.tableData.page});
		state.groupData = row.data.groutDate;
		if (!state.configuration_group_id && state.groupData?.length) handleSelect(state.groupData[0]?.id);
		state.tableData = row.data.data;
		state.loading = false;
	};
	const getNowData = computed(() => {
		return state.tableData?.filter(e => (e.configuration_group_id + '') == state.configuration_group_id);
	})
	const groupInput = ref();
	const toAddTag = async () => {
		state.addGroup = true;
		groupInput.value.focus();
	}
	let t1 : any = null;
	const blurInp = () => {
		t1 = setTimeout(() => {
			state.addGroup = false;
		}, 350)
	}
	const savesubInfo = async (item : any) => {
		clearTimeout(t1);
		state.loading = true;
		try {
			let row = await saveConfigurationInfo(item);
			if (row.code == 200) {
				ElMessage.success('操作成功');
				item.edit = false;
				item.id = false;
				getTableData();
			}
		} catch (e) {
			console.log(e, 'saveConfigurationInfo')
			//TODO handle the exception
		}
		state.loading = false;
		state.addGroup = false;
	}
	const saveInfo = async (e) => {
		clearTimeout(t1);
		state.loading = true;
		try {
			let row = await saveConfigurationGroupInfo(state.fromGroupData);
			if (row.code == 200) {
				ElMessage('操作成功');
				state.addGroup = false;
				state.fromGroupData.name = '';
				getTableData();
			}
		} catch (e) {
			console.log(e, 'saveConfigurationInfo')
			//TODO handle the exception
		}
		state.loading = false;
		state.addGroup = false;
	}
	// 页面加载时
	onMounted(() => {
		getTableData();
	});
</script>