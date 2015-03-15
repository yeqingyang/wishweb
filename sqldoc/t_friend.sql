create table t_friend(
	uid int unsigned not null comment 'uid',
	fuid int unsigned not null comment 'fuid',
	friend_type int unsigned not null comment '类型',
	status tinyint unsigned not null comment '状态',
	primary key(uid, fuid),
	index(fuid, uid)
)engine = InnoDb default charset utf8;
