

CREATE TABLE public.visitas (
	id serial4 NOT NULL,
	img varchar(200) NOT NULL,
	data_criacao timestamp DEFAULT now() NOT NULL,
	CONSTRAINT visitas_pk PRIMARY KEY (id)
);