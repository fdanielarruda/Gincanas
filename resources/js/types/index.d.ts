import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    type: number;
}

interface Colocation {
    place: string;
    points: number;
}

interface Gymkhana {
    id: number;
    title: string;
    phases: Phase[];
}

interface Phase {
    id: number;
    title: string;
    abbreviation: string;
    description: string;
    type: number;
    order: number | null;
    criteria: string[] | null;
    colocations: Colocation[] | null;
    checklist_colocations: Colocation[] | null;
    ginkhana: Gymkhana;
}

interface Team {
    id: number;
    title: string;
    participants: string[];
}

interface Judge {
    id: number;
    name: string;
}
export interface ResultData {
    [teamId: number]: {
        [phaseId: number]: any;
    };
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
