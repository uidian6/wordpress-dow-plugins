( function( wp ) {
	const { registerBlockType } = wp.blocks;
	const { TextControl, SelectControl } = wp.components;

	registerBlockType('fox/cloud-download', {
		title: '附件下载卡片',
		icon: 'download',
		category: 'widgets',
		attributes: {
			url: { type: 'string' },
			name: { type: 'string' },
			code: { type: 'string' },
			theme: { type: 'string', default: 'blueviolet' }
		},
		edit: ({ attributes, setAttributes }) => {
			return wp.element.createElement("div", {
				style: { border:"1px solid #ddd",padding:"12px",borderRadius:"10px",background:"#f9fafb" }
			},
				wp.element.createElement(TextControl,{label:"文件名",value:attributes.name||'',onChange:v=>setAttributes({name:v})}),
				wp.element.createElement(TextControl,{label:"下载链接",value:attributes.url||'',onChange:v=>setAttributes({url:v})}),
				wp.element.createElement(TextControl,{label:"提取码（可选）",value:attributes.code||'',onChange:v=>setAttributes({code:v})}),
				wp.element.createElement(SelectControl,{
					label:"渐变主题",
					value:attributes.theme,
					options:[
						{label:"蓝紫（默认）", value:"blueviolet"},
						{label:"青绿", value:"green"},
						{label:"橙红", value:"orange"}
					],
					onChange:v=>setAttributes({theme:v})
				}),
				wp.element.createElement("p",{style:{color:"#6b7280",fontSize:"13px"}}, "前端自适应布局，自动识别云盘。")
			);
		},
		save: () => null
	});
} )( window.wp );
