/**
 * edatagrid - jQuery EasyUI
 * 
 * Licensed under the GPL:
 *   http://www.gnu.org/licenses/gpl.txt
 *
 * Copyright 2011 stworthy [ stworthy@gmail.com ] 
 * 
 * Dependencies:
 *   datagrid
 *   messager
 * 
 */
(function($){
	var currTarget;
	/* ----------------------------------------------------------------------------------- */
	//页面载入完成后，绑定事件
	$(function(){
		$(document).unbind('.edatagrid').bind('mousedown.edatagrid', function(e){
			var p = $(e.target).closest('div.datagrid-view,div.combo-panel');
			if (p.length){
				if (p.hasClass('datagrid-view')){
					var dg = p.children('table');
					if (dg.length && currTarget != dg[0]){
						_save();
					}
				}
				return;
			}
			_save();
			function _save(){
				var dg = $(currTarget);
				if (dg.length){
					dg.edatagrid('saveRow');
					currTarget = undefined;
				}
			}
		});
	});
	/* ----------------------------------------------------------------------------------- */
	/**
	 * 创建插件对象辅助方法
	 * @param target 当前的Gird对象
     */
	function buildGrid(target){
		var opts = $.data(target, 'edatagrid').options; // 获取options对象
		/**
		 * onDblClickCell()
		 * onClickCell
		 *
         */
		$(target).datagrid($.extend({}, opts, {
			onDblClickCell:function(index,field){
				if (opts.editing){
					$(this).edatagrid('editRow', index);
					focusEditor(field);
				}
			},
			onClickCell:function(index,field){
				if (opts.editing && opts.editIndex >= 0){
					$(this).edatagrid('editRow', index);
					focusEditor(field);
				}
			},
			onAfterEdit: function(index, row){
				opts.editIndex = -1;
				var url = row.isNewRecord ? opts.saveUrl : opts.updateUrl;
				if (url){
					$.post(url, row, function(data){
						if (data.isError){
							$(target).edatagrid('cancelRow',index);
							$(target).edatagrid('selectRow',index);
							$(target).edatagrid('editRow',index);
							opts.onError.call(target, index, data);
							return;
						}
						data.isNewRecord = null;
						$(target).datagrid('updateRow', {
							index: index,
							row: data
						});
						if (opts.tree){
							var idValue = row[opts.idField||'id'];
							var t = $(opts.tree);
							var node = t.tree('find', idValue);
							if (node){
								node.text = row[opts.treeTextField];
								t.tree('update', node);
							} else {
								var pnode = t.tree('find', row[opts.treeParentField]);
								t.tree('append', {
									parent: (pnode ? pnode.target : null),
									data: [{id:idValue,text:row[opts.treeTextField]}]
								});
							}
						}
						opts.onSave.call(target, index, row);
					},'json');
				} else {
					opts.onSave.call(target, index, row);
				}
				if (opts.onAfterEdit) opts.onAfterEdit.call(target, index, row);
			},
			onCancelEdit: function(index, row){
				opts.editIndex = -1;
				if (row.isNewRecord) {
					$(this).datagrid('deleteRow', index);
				}
				if (opts.onCancelEdit) opts.onCancelEdit.call(target, index, row);
			},
			onBeforeLoad: function(param){
				if (opts.onBeforeLoad.call(target, param) == false){return false}
//				$(this).datagrid('rejectChanges');
				$(this).edatagrid('cancelRow');
				if (opts.tree){
					var node = $(opts.tree).tree('getSelected');
					param[opts.treeParentField] = node ? node.id : undefined;
				}
			}
		}));
		
		//让当前编辑器获得焦点
		function focusEditor(field){
			var editor = $(target).datagrid('getEditor', {index:opts.editIndex,field:field});
			if (editor){
				editor.target.focus();
			} else {
				var editors = $(target).datagrid('getEditors', opts.editIndex);
				if (editors.length){
					editors[0].target.focus();
				}
			}
		}
		
		if (opts.tree){
			$(opts.tree).tree({
				url: opts.treeUrl,
				onClick: function(node){
					$(target).datagrid('load');
				},
				onDrop: function(dest,source,point){
					var targetId = $(this).tree('getNode', dest).id;
					$.ajax({
						url: opts.treeDndUrl,
						type:'post',
						data:{
							id:source.id,
							targetId:targetId,
							point:point
						},
						dataType:'json',
						success:function(){
							$(target).datagrid('load');
						}
					});
				}
			});
		}
	}
	/* ----------------------------------------------------------------------------------- */
	/**
	 * 创建一个叫edatagrid插件
	 * @param options 插件选项，如果为字符串如$('#dg').edatagrid('getSelections')则调用getSelections方法
	 * @param param  插件参数
	 * @returns {*}
     */
	$.fn.edatagrid = function(options, param){
		// 如果options是字符串
		if (typeof options == 'string'){
			var method = $.fn.edatagrid.methods[options];
			if (method){
				return method(this, param);
			} else {
				return this.datagrid(options, param);
			}
		}
		options = options || {};
		return this.each(function(){
			var state = $.data(this, 'edatagrid');
			if (state){
				$.extend(state.options, options);
			} else {
				$.data(this, 'edatagrid', {
					options: $.extend({}, $.fn.edatagrid.defaults, $.fn.edatagrid.parseOptions(this), options)
				});
			}
			buildGrid(this);
		});
	};
	/* edatagrid 对象增加一个parseOptions()方法 */
	$.fn.edatagrid.parseOptions = function(target){
		return $.extend({}, $.fn.datagrid.parseOptions(target), {
		});
	};
	/* ----------------------------------------------------------------------------------- */
	/**
	 * 为edatagrid 对象重构其中的方法
	 * @type {{
	 * options: $.fn.edatagrid.methods.options,
	 * enableEditing: $.fn.edatagrid.methods.enableEditing,
	 * disableEditing: $.fn.edatagrid.methods.disableEditing,
	 * editRow: $.fn.edatagrid.methods.editRow,
	 * addRow: $.fn.edatagrid.methods.addRow,
	 * saveRow: $.fn.edatagrid.methods.saveRow,
	 * cancelRow: $.fn.edatagrid.methods.cancelRow,
	 * destroyRow: $.fn.edatagrid.methods.destroyRow,
	 * destroyRows: $.fn.edatagrid.methods.destroyRows}}
     */
	$.fn.edatagrid.methods = {
		options: function(jq){
			var opts = $.data(jq[0], 'edatagrid').options;
			return opts;
		},
		enableEditing: function(jq){
			return jq.each(function(){
				var opts = $.data(this, 'edatagrid').options;
				opts.editing = true;
			});
		},
		disableEditing: function(jq){
			return jq.each(function(){
				var opts = $.data(this, 'edatagrid').options;
				opts.editing = false;
			});
		},
		/* 编辑行*/
		editRow: function(jq, index){
			return jq.each(function(){
				var dg = $(this);
				var opts = $.data(this, 'edatagrid').options;
				var editIndex = opts.editIndex;
				if (editIndex != index){
					if (dg.datagrid('validateRow', editIndex)){
						if (editIndex>=0){
							if (opts.onBeforeSave.call(this, editIndex) == false) {
								setTimeout(function(){
									dg.datagrid('selectRow', editIndex);
								},0);
								return;
							}
						}
						dg.datagrid('endEdit', editIndex);
						dg.datagrid('beginEdit', index);
						opts.editIndex = index;
						
						if (currTarget != this && $(currTarget).length){
							$(currTarget).edatagrid('saveRow');
							currTarget = undefined;
						}
						if (opts.autoSave){
							currTarget = this;
						}
						
						var rows = dg.datagrid('getRows');
						opts.onEdit.call(this, index, rows[index]);
					} else {
						setTimeout(function(){
							dg.datagrid('selectRow', editIndex);
						}, 0);
					}
				}
			});
		},
		/* 添加新行 */
		addRow: function(jq, index){
			return jq.each(function(){
				var dg = $(this);
				var opts = $.data(this, 'edatagrid').options;
				if (opts.editIndex >= 0){
                    //验证指定的行，成功返回true
					if (!dg.datagrid('validateRow', opts.editIndex)){
						dg.datagrid('selectRow', opts.editIndex);
						return;
					}
                    //调用OnBeforeSave函数，返回false则表示取消保存
					if (opts.onBeforeSave.call(this, opts.editIndex) == false){
						setTimeout(function(){
							dg.datagrid('selectRow', opts.editIndex);
						},0);
						return;
					}
                    //结束编辑
					dg.datagrid('endEdit', opts.editIndex);
				}
                //返回当前页的记录
				var rows = dg.datagrid('getRows');
				
				function _add(index, row){
					if (index == undefined){
						dg.datagrid('appendRow', row);
						opts.editIndex = rows.length - 1; //
					} else {
						dg.datagrid('insertRow', {index:index,row:row});
						opts.editIndex = index;
					}
				}
				if (typeof index == 'object'){
					_add(index.index, $.extend(index.row, {isNewRecord:true}))
				} else {
					_add(index, {isNewRecord:true});
				}
				
//				if (index == undefined){
//					dg.datagrid('appendRow', {isNewRecord:true});
//					opts.editIndex = rows.length - 1;
//				} else {
//					dg.datagrid('insertRow', {
//						index: index,
//						row: {isNewRecord:true}
//					});
//					opts.editIndex = index;
//				}
				
				dg.datagrid('beginEdit', opts.editIndex);
				dg.datagrid('selectRow', opts.editIndex);
				
				if (opts.tree){
					var node = $(opts.tree).tree('getSelected');
					rows[opts.editIndex][opts.treeParentField] = (node ? node.id : 0);
				}
				//调用onAdd函数执行真正添加操作
				opts.onAdd.call(this, opts.editIndex, rows[opts.editIndex]);
			});
		},
		/* 保存 */
		saveRow: function(jq){
			return jq.each(function(){
				var dg = $(this);
				var opts = $.data(this, 'edatagrid').options;
				if (opts.editIndex >= 0){
					if (opts.onBeforeSave.call(this, opts.editIndex) == false) {
						setTimeout(function(){
							dg.datagrid('selectRow', opts.editIndex);
						},0);
						return;
					}
					$(this).datagrid('endEdit', opts.editIndex);
				}
			});
		},
		/*
		取消
		*/
		
		cancelRow: function(jq){
			return jq.each(function(){
				var opts = $.data(this, 'edatagrid').options;
				if (opts.editIndex >= 0){
					$(this).datagrid('cancelEdit', opts.editIndex);
				}
			});
		},
		/*-----------------------------------------------------
		 删除一行或多行
		*/
		destroyRow: function(jq, index){
			return jq.each(function(){
				var dg = $(this);
				var opts = $.data(this, 'edatagrid').options;
				
				var rows = [];
				if (index == undefined){
					rows = dg.datagrid('getSelections');
				} else {
					var rowIndexes = $.isArray(index) ? index : [index];
					for(var i=0; i<rowIndexes.length; i++){
						var row = opts.finder.getRow(this, rowIndexes[i]);
						if (row){
							rows.push(row);
						}
					}
				}
				
				if (!rows.length){
					$.messager.show({
						title: opts.destroyMsg.norecord.title,
						msg: opts.destroyMsg.norecord.msg
					});
					return;
				}
				
				$.messager.confirm(opts.destroyMsg.confirm.title,opts.destroyMsg.confirm.msg,function(r){
					if (r){
						for(var i=0; i<rows.length; i++){
							_del(rows[i]);
						}
						dg.datagrid('clearSelections');
					}
				});
				// 删除一行
				function _del(row){
					var index = dg.datagrid('getRowIndex', row);
					if (index == -1){return}
					if (row.isNewRecord){
						dg.datagrid('cancelEdit', index);
					} else {
						if (opts.destroyUrl){
							var idValue = row[opts.idField||'id'];
							$.post(opts.destroyUrl, {id:idValue}, function(data){
								var index = dg.datagrid('getRowIndex', idValue);
								if (data.isError){
									dg.datagrid('selectRow', index);
									opts.onError.call(dg[0], index, data);
									return;
								}
								if (opts.tree){
									dg.datagrid('reload');
									var t = $(opts.tree);
									var node = t.tree('find', idValue);
									if (node){
										t.tree('remove', node.target);
									}
								} else {
									dg.datagrid('cancelEdit', index);
									dg.datagrid('deleteRow', index);
								}
								opts.onDestroy.call(dg[0], index, row);
							}, 'json');
						} else {
							dg.datagrid('cancelEdit', index);
							dg.datagrid('deleteRow', index);
							opts.onDestroy.call(dg[0], index, row);
						}
					}
				}
				
			});
		},

        /**
         * 批量删除的代码
         * @param jq: jquery对象
         * @param index：
         * @returns {*}
         */
		destroyRows:function(jq, index){
			return jq.each(function(){
				var dg = $(this);
				var opts = $.data(this, 'edatagrid').options;
				// 返回所有被选择的行，当没有记录被选择时，将返回一个空数组。
				var rows = [];
				if (index == undefined){
					//rows = dg.datagrid('getSelections');
                    rows = dg.datagrid('getChecked');
				} else {
					var rowIndexes = $.isArray(index) ? index : [index];
					for(var i=0; i<rowIndexes.length; i++){
						var row = opts.finder.getRow(this, rowIndexes[i]);
						if (row){
							rows.push(row);
						}
					}
				}
                //
				if (!rows.length){
					$.messager.show({
						title: opts.destroyMsg.norecord.title,
						msg: opts.destroyMsg.norecord.msg,
						timeout:5000
					});
					return;
				}
				$.messager.confirm(opts.destroyMsg.confirm.title,"你是否需要删除选中的"+rows.length+"条记录?",function(r){
					if (r){
                        //如果定义了删除URL
                       	if (opts.batchDeleteUrl){
							var ids = [];
							for (var i = 0; i < rows.length; i++) {
								ids.push(rows[i][opts.idField||'id']);
							}

							$.post(
								opts.batchDeleteUrl,{id: ids, timeStamp: new Date().getTime()},
								function (jsonStr) {
									//var json = eval("("+jsonStr+")");
									if (jsonStr.result == true) {
                                        $.messager.alert('信息', jsonStr.msg,'info');
									} else {
										$.messager.alert('信息', jsonStr.msg,'error');
									}
                                    dg.datagrid('clearSelections');
                                    dg.datagrid('load');
								},'json'
							);

						}
					}
				});

			});
		}
		
		
	};
	/* ----------------------------------------------------------------------------------- */
	/**
	 * 扩展datagrid的default选项，增加了一些自己定义的key/value属性
     */
	$.fn.edatagrid.defaults = $.extend({}, $.fn.datagrid.defaults, {
		editing: true,
		editIndex: -1,
		destroyMsg:{
			norecord:{
				title:'警告',
				msg:'请至少选择一条记录.'
			},
			confirm:{
				title:'确定',
				msg:'你是否需要删除选中的记录?'
			}
		},
//		destroyConfirmTitle: 'Confirm',
//		destroyConfirmMsg: 'Are you sure you want to delete?',
		autoSave: false,	// auto save the editing row when click out of datagrid
		url: null,	        //返回获取JSON数据的url
		saveUrl: null,	    //return the added row
		updateUrl: null,	// return the updated row
		destroyUrl: null,	// return {success:true}
		
		tree: null,		// the tree selector
		treeUrl: null,	// return tree data
		treeDndUrl: null,	// to process the drag and drop operation, return {success:true}
		treeTextField: 'name',
		treeParentField: 'parentId',
		//------------------------------
		loadMsg: '数据加载中请稍后……',
			singleSelect: true,
			fitColumns: true,
			nowrap: true,
			pagination: true,
			rownumbers: true,
			pageSize:20,
        checkOnSelect:false,
        selectOnCheck:false,
		pageList:[20,30,40],
		batchDeleteUrl:null, //批量删除URL
        /*loader:function(param,sucess,error){

                // alert('ddd');
                return true;
        },
        loadFilter: function(data){
            alert(data);
            if (data.d){
                return data.d;
            } else {
                return data;
            }
        },*/

        //--------默认的事件处理-----------------
        onLoadSuccess:function(data){
            if(data.total==0){
              //  $.messager.alert('信息', '没有符合条件的记录！','info');
            }
        },
        onLoadError:function(data){
            $.messager.alert('信息', '数据格式不符合要求，加载错误！','error');
        },
		onAdd: function(index, row){},
		onEdit: function(index, row){},
		onBeforeSave: function(index){},
		onSave: function(index, row){},
		onDestroy: function(index, row){},
		onError: function(index, row){}
	});
})(jQuery);