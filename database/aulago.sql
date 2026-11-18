
create database if not exists aulago
character set utf8mb4
collate utf8mb4_unicode_ci;

use aulago;


-- 1. ROLES

create table roles (
    id_rol tinyint unsigned auto_increment primary key,
    nombre varchar(20) not null unique
) engine = InnoDB;

insert into roles (nombre)
values
    ('estudiante'),
    ('instructor'),
    ('administrador');


-- 2. USUARIOS

create table usuarios (
    id_usuario int unsigned auto_increment primary key,
    id_rol tinyint unsigned not null,
    nombre varchar(150) not null,
    genero enum(
        'mujer',
        'hombre',
        'otro',
        'prefiero_no_decir'
    ) not null,
    fecha_nacimiento date not null,
    avatar varchar(255) not null,
    email varchar(255) not null unique,
    contrasena_hash varchar(255) not null,
    intentos_fallidos tinyint unsigned not null default 0,
    bloqueado tinyint unsigned not null default 0,
    fecha_registro datetime not null default current_timestamp,
    fecha_actualizacion datetime not null default current_timestamp
        on update current_timestamp,

    constraint fk_usuarios_roles
        foreign key (id_rol) references roles(id_rol)
        on delete restrict
        on update cascade
) engine = InnoDB;


-- 3. CATEGORIAS

create table categorias (
    id_categoria int unsigned auto_increment primary key,
    nombre varchar(100) not null,
    slug varchar(120) not null unique,
    descripcion text,
    activo tinyint not null default 1
) engine = InnoDB;


-- 4. CURSOS

create table cursos (
    id_curso int unsigned auto_increment primary key,
    id_instructor int unsigned not null,
    id_categoria int unsigned not null,
    titulo varchar(200) not null,
    slug varchar(220) not null unique,
    descripcion text not null,
    imagen varchar(255),
    precio decimal(10,2) not null default 0.00,
    moneda char(3) not null default 'USD',
    estado enum(
        'borrador',
        'publicado',
        'desactivado'
    ) not null default 'borrador',
    fecha_creacion datetime not null default current_timestamp,
    fecha_actualizacion datetime not null default current_timestamp
        on update current_timestamp,

    foreign key (id_instructor)
        references usuarios(id_usuario),

    foreign key (id_categoria)
        references categorias(id_categoria),

    check (precio >= 0)
) engine = InnoDB;


-- 5. NIVELES

create table niveles (
    id_nivel int unsigned auto_increment primary key,
    id_curso int unsigned not null,
    titulo varchar(150) not null,
    descripcion text,
    orden int unsigned not null,
    activo tinyint not null default 1,

    foreign key (id_curso)
        references cursos(id_curso),

    unique (id_curso, orden)
) engine = InnoDB;


-- 6. LECCIONES

create table lecciones (
    id_leccion int unsigned auto_increment primary key,
    id_nivel int unsigned not null,
    titulo varchar(200) not null,
    contenido longtext,
    url_video varchar(500),
    orden int unsigned not null,
    activo tinyint not null default 1,

    foreign key (id_nivel)
        references niveles(id_nivel),

    unique (id_nivel, orden)
) engine = InnoDB;


-- 7. INSCRIPCIONES

create table inscripciones (
    id_inscripcion int unsigned auto_increment primary key,
    id_estudiante int unsigned not null,
    id_curso int unsigned not null,
    estado enum(
        'activa',
        'completada',
        'cancelada'
    ) not null default 'activa',
    fecha_inscripcion datetime not null default current_timestamp,
    fecha_finalizacion datetime,

    foreign key (id_estudiante)
        references usuarios(id_usuario),

    foreign key (id_curso)
        references cursos(id_curso),

    unique (id_estudiante, id_curso)
) engine = InnoDB;


-- 8. PAGOS

create table pagos (
    id_pago int unsigned auto_increment primary key,
    id_estudiante int unsigned not null,
    id_curso int unsigned not null,
    id_inscripcion int unsigned,
    metodo varchar(40) not null,
    referencia_externa varchar(150),
    monto decimal(10,2) not null,
	moneda char(3) not null default 'MXN',
    estado enum(
        'pendiente',
        'completado',
        'fallido',
        'reembolsado'
    ) not null default 'pendiente',
    fecha_pago datetime not null default current_timestamp,

    foreign key (id_estudiante)
        references usuarios(id_usuario),

    foreign key (id_curso)
        references cursos(id_curso),

    foreign key (id_inscripcion)
        references inscripciones(id_inscripcion),

    unique (metodo, referencia_externa),

    check (monto >= 0)
) engine = InnoDB;

use aulago;


-- 9. PROGRESO DE LECCIONES

create table progreso_lecciones (
    id_progreso int unsigned auto_increment primary key,
    id_inscripcion int unsigned not null,
    id_leccion int unsigned not null,
    completada tinyint not null default 0,
    fecha_completada datetime,

    foreign key (id_inscripcion)
        references inscripciones(id_inscripcion),

    foreign key (id_leccion)
        references lecciones(id_leccion),

    unique (id_inscripcion, id_leccion)
) engine = InnoDB;


-- 10. CERTIFICADOS

create table certificados (
    id_certificado int unsigned auto_increment primary key,
    id_inscripcion int unsigned not null unique,
    codigo varchar(100) not null unique,
    fecha_emision datetime not null default current_timestamp,

    foreign key (id_inscripcion)
        references inscripciones(id_inscripcion)
) engine = InnoDB;


-- 11. RESEñAS

create table resenas (
    id_resena int unsigned auto_increment primary key,
    id_estudiante int unsigned not null,
    id_curso int unsigned not null,
    calificacion tinyint unsigned not null,
    comentario text,
    estado enum(
        'visible',
        'oculta'
    ) not null default 'visible',
    fecha_creacion datetime not null default current_timestamp,

    foreign key (id_estudiante)
        references usuarios(id_usuario),

    foreign key (id_curso)
        references cursos(id_curso),

    unique (id_estudiante, id_curso),

    check (calificacion between 1 and 5)
) engine = InnoDB;


-- 12. CONVERSACIONES

create table conversaciones (
    id_conversacion int unsigned auto_increment primary key,
    id_estudiante int unsigned not null,
    id_instructor int unsigned not null,
    id_curso int unsigned not null,
    fecha_creacion datetime not null default current_timestamp,

    foreign key (id_estudiante)
        references usuarios(id_usuario),

    foreign key (id_instructor)
        references usuarios(id_usuario),

    foreign key (id_curso)
        references cursos(id_curso),

    unique (id_estudiante, id_instructor, id_curso)
) engine = InnoDB;


-- 13. MENSAJES

create table mensajes (
    id_mensaje int unsigned auto_increment primary key,
    id_conversacion int unsigned not null,
    id_remitente int unsigned not null,
    contenido text not null,
    fecha_envio datetime not null default current_timestamp,
    leido tinyint not null default 0,

    foreign key (id_conversacion)
        references conversaciones(id_conversacion),

    foreign key (id_remitente)
        references usuarios(id_usuario)
) engine = InnoDB;


-- CONSULTAS PARA REVISAR LA ESTRUCTURA

show tables;

select * from roles;
