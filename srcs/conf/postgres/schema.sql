--
-- PostgreSQL database dump
--

-- Dumped from database version 15.12 (Debian 15.12-1.pgdg120+1)
-- Dumped by pg_dump version 15.12 (Debian 15.12-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: pg_trgm; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_trgm WITH SCHEMA public;


--
-- Name: EXTENSION pg_trgm; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pg_trgm IS 'text similarity measurement and index searching based on trigrams';


--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: actors; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.actors (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    biography text,
    photo_url character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.actors OWNER TO kaizen;

--
-- Name: actors_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.actors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.actors_id_seq OWNER TO kaizen;

--
-- Name: actors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.actors_id_seq OWNED BY public.actors.id;


--
-- Name: admin_users; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.admin_users (
    id bigint NOT NULL,
    username character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    admin_level integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.admin_users OWNER TO kaizen;

--
-- Name: admin_users_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.admin_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.admin_users_id_seq OWNER TO kaizen;

--
-- Name: admin_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.admin_users_id_seq OWNED BY public.admin_users.id;


--
-- Name: booking_seats; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.booking_seats (
    id bigint NOT NULL,
    booking_id bigint NOT NULL,
    seat_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.booking_seats OWNER TO kaizen;

--
-- Name: booking_seats_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.booking_seats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.booking_seats_id_seq OWNER TO kaizen;

--
-- Name: booking_seats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.booking_seats_id_seq OWNED BY public.booking_seats.id;


--
-- Name: bookings; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.bookings (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    booking_id character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.bookings OWNER TO kaizen;

--
-- Name: bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bookings_id_seq OWNER TO kaizen;

--
-- Name: bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.bookings_id_seq OWNED BY public.bookings.id;


--
-- Name: cinemas; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.cinemas (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    location character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cinemas OWNER TO kaizen;

--
-- Name: cinemas_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.cinemas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cinemas_id_seq OWNER TO kaizen;

--
-- Name: cinemas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.cinemas_id_seq OWNED BY public.cinemas.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO kaizen;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO kaizen;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: functions; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.functions (
    id bigint NOT NULL,
    movie_id bigint NOT NULL,
    room_id bigint NOT NULL,
    date date NOT NULL,
    "time" time(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.functions OWNER TO kaizen;

--
-- Name: functions_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.functions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.functions_id_seq OWNER TO kaizen;

--
-- Name: functions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.functions_id_seq OWNED BY public.functions.id;


--
-- Name: genres; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.genres (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.genres OWNER TO kaizen;

--
-- Name: genres_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.genres_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.genres_id_seq OWNER TO kaizen;

--
-- Name: genres_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.genres_id_seq OWNED BY public.genres.id;


--
-- Name: manages; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.manages (
    admin_id bigint NOT NULL,
    cinema_id bigint NOT NULL
);


ALTER TABLE public.manages OWNER TO kaizen;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO kaizen;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO kaizen;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: movie_actor; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.movie_actor (
    movie_id bigint NOT NULL,
    actor_id bigint NOT NULL
);


ALTER TABLE public.movie_actor OWNER TO kaizen;

--
-- Name: movie_genre; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.movie_genre (
    movie_id bigint NOT NULL,
    genre_id bigint NOT NULL
);


ALTER TABLE public.movie_genre OWNER TO kaizen;

--
-- Name: movies; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.movies (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    synopsis text,
    duration integer NOT NULL,
    rating character varying(50),
    release_date date,
    photo_url character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.movies OWNER TO kaizen;

--
-- Name: movies_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.movies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.movies_id_seq OWNER TO kaizen;

--
-- Name: movies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.movies_id_seq OWNED BY public.movies.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO kaizen;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO kaizen;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO kaizen;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: rooms; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.rooms (
    id bigint NOT NULL,
    cinema_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    capacity integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.rooms OWNER TO kaizen;

--
-- Name: rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.rooms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.rooms_id_seq OWNER TO kaizen;

--
-- Name: rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.rooms_id_seq OWNED BY public.rooms.id;


--
-- Name: seats; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.seats (
    id bigint NOT NULL,
    function_id bigint NOT NULL,
    number integer NOT NULL,
    seat_row character varying(10) NOT NULL,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT seats_status_check CHECK (((status)::text = ANY ((ARRAY['available'::character varying, 'reserved'::character varying, 'occupied'::character varying])::text[])))
);


ALTER TABLE public.seats OWNER TO kaizen;

--
-- Name: seats_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.seats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seats_id_seq OWNER TO kaizen;

--
-- Name: seats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.seats_id_seq OWNED BY public.seats.id;


--
-- Name: telescope_entries; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.telescope_entries (
    sequence bigint NOT NULL,
    uuid uuid NOT NULL,
    batch_id uuid NOT NULL,
    family_hash character varying(255),
    should_display_on_index boolean DEFAULT true NOT NULL,
    type character varying(20) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.telescope_entries OWNER TO kaizen;

--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.telescope_entries_sequence_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.telescope_entries_sequence_seq OWNER TO kaizen;

--
-- Name: telescope_entries_sequence_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.telescope_entries_sequence_seq OWNED BY public.telescope_entries.sequence;


--
-- Name: telescope_entries_tags; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.telescope_entries_tags (
    entry_uuid uuid NOT NULL,
    tag character varying(255) NOT NULL
);


ALTER TABLE public.telescope_entries_tags OWNER TO kaizen;

--
-- Name: telescope_monitoring; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.telescope_monitoring (
    tag character varying(255) NOT NULL
);


ALTER TABLE public.telescope_monitoring OWNER TO kaizen;

--
-- Name: users; Type: TABLE; Schema: public; Owner: kaizen
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    username character varying(255) NOT NULL,
    birthdate date
);


ALTER TABLE public.users OWNER TO kaizen;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: kaizen
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO kaizen;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kaizen
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: actors id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.actors ALTER COLUMN id SET DEFAULT nextval('public.actors_id_seq'::regclass);


--
-- Name: admin_users id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.admin_users ALTER COLUMN id SET DEFAULT nextval('public.admin_users_id_seq'::regclass);


--
-- Name: booking_seats id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.booking_seats ALTER COLUMN id SET DEFAULT nextval('public.booking_seats_id_seq'::regclass);


--
-- Name: bookings id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.bookings ALTER COLUMN id SET DEFAULT nextval('public.bookings_id_seq'::regclass);


--
-- Name: cinemas id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.cinemas ALTER COLUMN id SET DEFAULT nextval('public.cinemas_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: functions id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.functions ALTER COLUMN id SET DEFAULT nextval('public.functions_id_seq'::regclass);


--
-- Name: genres id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.genres ALTER COLUMN id SET DEFAULT nextval('public.genres_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: movies id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movies ALTER COLUMN id SET DEFAULT nextval('public.movies_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: rooms id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.rooms ALTER COLUMN id SET DEFAULT nextval('public.rooms_id_seq'::regclass);


--
-- Name: seats id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.seats ALTER COLUMN id SET DEFAULT nextval('public.seats_id_seq'::regclass);


--
-- Name: telescope_entries sequence; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.telescope_entries ALTER COLUMN sequence SET DEFAULT nextval('public.telescope_entries_sequence_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: actors actors_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.actors
    ADD CONSTRAINT actors_pkey PRIMARY KEY (id);


--
-- Name: admin_users admin_users_email_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_email_unique UNIQUE (email);


--
-- Name: admin_users admin_users_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_pkey PRIMARY KEY (id);


--
-- Name: admin_users admin_users_username_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_username_unique UNIQUE (username);


--
-- Name: booking_seats booking_seats_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.booking_seats
    ADD CONSTRAINT booking_seats_pkey PRIMARY KEY (id);


--
-- Name: bookings bookings_booking_id_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_booking_id_unique UNIQUE (booking_id);


--
-- Name: bookings bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_pkey PRIMARY KEY (id);


--
-- Name: cinemas cinemas_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.cinemas
    ADD CONSTRAINT cinemas_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: functions functions_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.functions
    ADD CONSTRAINT functions_pkey PRIMARY KEY (id);


--
-- Name: genres genres_name_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.genres
    ADD CONSTRAINT genres_name_unique UNIQUE (name);


--
-- Name: genres genres_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.genres
    ADD CONSTRAINT genres_pkey PRIMARY KEY (id);


--
-- Name: manages manages_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.manages
    ADD CONSTRAINT manages_pkey PRIMARY KEY (admin_id, cinema_id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: movie_actor movie_actor_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movie_actor
    ADD CONSTRAINT movie_actor_pkey PRIMARY KEY (movie_id, actor_id);


--
-- Name: movie_genre movie_genre_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movie_genre
    ADD CONSTRAINT movie_genre_pkey PRIMARY KEY (movie_id, genre_id);


--
-- Name: movies movies_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movies
    ADD CONSTRAINT movies_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: rooms rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_pkey PRIMARY KEY (id);


--
-- Name: seats seats_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.seats
    ADD CONSTRAINT seats_pkey PRIMARY KEY (id);


--
-- Name: telescope_entries telescope_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_pkey PRIMARY KEY (sequence);


--
-- Name: telescope_entries_tags telescope_entries_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_pkey PRIMARY KEY (entry_uuid, tag);


--
-- Name: telescope_entries telescope_entries_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.telescope_entries
    ADD CONSTRAINT telescope_entries_uuid_unique UNIQUE (uuid);


--
-- Name: telescope_monitoring telescope_monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.telescope_monitoring
    ADD CONSTRAINT telescope_monitoring_pkey PRIMARY KEY (tag);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_username_unique; Type: CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_unique UNIQUE (username);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: kaizen
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: telescope_entries_batch_id_index; Type: INDEX; Schema: public; Owner: kaizen
--

CREATE INDEX telescope_entries_batch_id_index ON public.telescope_entries USING btree (batch_id);


--
-- Name: telescope_entries_created_at_index; Type: INDEX; Schema: public; Owner: kaizen
--

CREATE INDEX telescope_entries_created_at_index ON public.telescope_entries USING btree (created_at);


--
-- Name: telescope_entries_family_hash_index; Type: INDEX; Schema: public; Owner: kaizen
--

CREATE INDEX telescope_entries_family_hash_index ON public.telescope_entries USING btree (family_hash);


--
-- Name: telescope_entries_tags_tag_index; Type: INDEX; Schema: public; Owner: kaizen
--

CREATE INDEX telescope_entries_tags_tag_index ON public.telescope_entries_tags USING btree (tag);


--
-- Name: telescope_entries_type_should_display_on_index_index; Type: INDEX; Schema: public; Owner: kaizen
--

CREATE INDEX telescope_entries_type_should_display_on_index_index ON public.telescope_entries USING btree (type, should_display_on_index);


--
-- Name: booking_seats booking_seats_booking_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.booking_seats
    ADD CONSTRAINT booking_seats_booking_id_foreign FOREIGN KEY (booking_id) REFERENCES public.bookings(id);


--
-- Name: booking_seats booking_seats_seat_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.booking_seats
    ADD CONSTRAINT booking_seats_seat_id_foreign FOREIGN KEY (seat_id) REFERENCES public.seats(id);


--
-- Name: bookings bookings_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: functions functions_movie_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.functions
    ADD CONSTRAINT functions_movie_id_foreign FOREIGN KEY (movie_id) REFERENCES public.movies(id);


--
-- Name: functions functions_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.functions
    ADD CONSTRAINT functions_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id);


--
-- Name: manages manages_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.manages
    ADD CONSTRAINT manages_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.admin_users(id);


--
-- Name: manages manages_cinema_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.manages
    ADD CONSTRAINT manages_cinema_id_foreign FOREIGN KEY (cinema_id) REFERENCES public.cinemas(id);


--
-- Name: movie_actor movie_actor_actor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movie_actor
    ADD CONSTRAINT movie_actor_actor_id_foreign FOREIGN KEY (actor_id) REFERENCES public.actors(id);


--
-- Name: movie_actor movie_actor_movie_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movie_actor
    ADD CONSTRAINT movie_actor_movie_id_foreign FOREIGN KEY (movie_id) REFERENCES public.movies(id);


--
-- Name: movie_genre movie_genre_genre_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movie_genre
    ADD CONSTRAINT movie_genre_genre_id_foreign FOREIGN KEY (genre_id) REFERENCES public.genres(id);


--
-- Name: movie_genre movie_genre_movie_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.movie_genre
    ADD CONSTRAINT movie_genre_movie_id_foreign FOREIGN KEY (movie_id) REFERENCES public.movies(id);


--
-- Name: rooms rooms_cinema_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_cinema_id_foreign FOREIGN KEY (cinema_id) REFERENCES public.cinemas(id);


--
-- Name: seats seats_function_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.seats
    ADD CONSTRAINT seats_function_id_foreign FOREIGN KEY (function_id) REFERENCES public.functions(id);


--
-- Name: telescope_entries_tags telescope_entries_tags_entry_uuid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: kaizen
--

ALTER TABLE ONLY public.telescope_entries_tags
    ADD CONSTRAINT telescope_entries_tags_entry_uuid_foreign FOREIGN KEY (entry_uuid) REFERENCES public.telescope_entries(uuid) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

