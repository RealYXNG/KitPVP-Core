-- #! sqlite
-- #{ players
-- #    { init
CREATE TABLE IF NOT EXISTS PLAYERS(uuid VARCHAR(255) PRIMARY KEY, rules int, class int, kit int, last_logged int, online_time int, cooldowns text, tokens int, skills text, skill_resets int);
-- #    }
-- #    { insert
-- # 	  :uuid string
-- #      :rules int
-- #      :class int
-- #      :kit int
-- #      :last_logged int
-- #      :online_time int
-- #      :cooldowns string
-- #      :tokens int
-- #      :skills string
-- #      :skill_resets int
INSERT INTO PLAYERS(
    uuid,
    rules,
    class,
    kit,
    last_logged,
    online_time,
    cooldowns,
    tokens,
    skills,
    skill_resets
) VALUES (
             :uuid,
             :rules,
             :class,
             :kit,
             :last_logged,
             :online_time,
             :cooldowns,
             :tokens,
             :skills,
             :skill_resets
         );
-- #    }
-- #    { update
-- # 	  :uuid string
-- #      :rules int
-- #      :class int
-- #      :kit int
-- #      :online_time int
-- #      :last_logged int
-- #      :cooldowns string
-- #      :tokens int
-- #      :skills string
-- #      :skill_resets int
UPDATE PLAYERS SET rules = :rules, class = :class, kit = :kit, cooldowns = :cooldowns, online_time = :online_time, last_logged = :last_logged, tokens = :tokens, skills = :skills, skill_resets = :skill_resets where uuid = :uuid;
-- #    }
-- #    { update_lastlogged
-- # 	  :uuid string
-- #      :last_logged int
UPDATE PLAYERS SET last_logged = :last_logged where uuid = :uuid;
-- #    }
-- #    { update_tokens
-- # 	  :uuid string
-- #      :tokens int
UPDATE PLAYERS SET tokens = :tokens where uuid = :uuid;
-- #    }
-- #    { isregistered
-- # 	  :uuid string
SELECT * FROM PLAYERS
WHERE uuid = :uuid;
-- #    }
-- #}
-- #{ koths
-- #    { init
CREATE TABLE IF NOT EXISTS KOTHS(x1 int, z1 int, x2 int, z2 int);
-- #    }
-- #    { add
-- # 	  :x1 int
-- # 	  :z1 int
-- # 	  :x2 int
-- # 	  :z2 int
INSERT INTO KOTHS(
    x1,
    z1,
    x2,
    z2
) VALUES (
             :x1,
             :z1,
             :x2,
             :z2
         );
-- #    }
-- #    { delete
DELETE FROM KOTHS;
-- #    }
-- #    { select
SELECT * FROM KOTHS;
-- #    }
-- #}