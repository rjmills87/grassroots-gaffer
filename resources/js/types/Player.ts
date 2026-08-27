export interface Guardian {
    id: number;
    name: string;
    email: string;
}

export interface Player {
    id: number;
    name: string;
    guardian_name: string;
    guardian_email: string;
    guardian_phone: string;
    team_id: number;
    pivot?: {
        player_response: 'attending' | 'unavailable' | null;
    };
    squad_number: number;
    position: string;
    guardians?: Guardian[];
}
