CREATE TABLE IF NOT EXISTS `t_group`
(
	`gid`	int(10) unsigned not null auto_increment comment '����id',
	`gname`	varchar(50) not null comment '��������',
	`uid`	int(10) unsigned not null comment '������uid',
	`create_time` int(10) unsigned not null comment '����ʱ��',
	`status` int(10) unsigned not null default 0 comment '״̬��0���� 1ɾ��',
	`va_user` blob not null comment 'members',
	primary key(`gid`)
) auto_increment=1000 default charset utf8 engine = InnoDb;
