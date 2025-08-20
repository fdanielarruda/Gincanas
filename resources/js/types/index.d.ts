import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    type: number;
}

interface Phase {
    id: number;
    title: string;
    type: number;
    criteria: string[] | null;
    colocations: Colocation[] | null;
    description: string;
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

interface Colocation {
    place: string;
    points: number;
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
