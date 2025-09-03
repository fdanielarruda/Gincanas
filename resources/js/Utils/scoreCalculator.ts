import { Phase, ResultData, Team, Judge, Colocation } from '@/types';

interface TeamWithScore extends Team {
    totalScore: number;
}

const TYPE_CRITERIA = 1;
const TYPE_COLOCATION = 3;
const TYPE_CHECKLIST = 4;

const calculateRankedPoints = (teams: Team[], phase: Phase, results: ResultData): { [teamId: number]: number } => {
    const scores: { [teamId: number]: number } = {};
    const teamsWithPhaseScore = teams.map(team => {
        let phaseScore = 0;
        const teamResults = results[team.id];

        if (!teamResults || teamResults[phase.id] === null || typeof teamResults[phase.id] === 'undefined') {
            return { ...team, phaseScore: 0 };
        }

        const phaseResult = teamResults[phase.id];

        // Esta função agora lida apenas com TYPE_CHECKLIST
        if (phase.type === TYPE_CHECKLIST && phase.checklist_colocations) {
            if (phaseResult && typeof phaseResult === 'object') {
                for (const colocationPlace in phaseResult) {
                    const colocation = phase.checklist_colocations.find(c => c.place === colocationPlace);
                    if (colocation) {
                        const quantity = Number(phaseResult[colocationPlace]) || 0;
                        phaseScore += quantity * colocation.points;
                    }
                }
            }
        }

        // A lógica de Colocation foi movida para calculatePhaseScore
        // A função calculateRankedPoints não é mais usada para isso

        return { ...team, phaseScore };
    });

    const sortedTeams = teamsWithPhaseScore.sort((a, b) => b.phaseScore - a.phaseScore);

    if (sortedTeams.length > 0 && sortedTeams[0].phaseScore === 0) {
        return scores;
    }

    sortedTeams.forEach((team, index) => {
        const championshipPoints = phase.colocations?.[index]?.points || 0;
        scores[team.id] = championshipPoints;
    });

    return scores;
};

export const calculatePhaseScore = (teamId: number, phase: Phase, results: ResultData, teams: Team[]): number => {
    const teamResults = results[teamId]?.[phase.id];
    if (teamResults === null || typeof teamResults === 'undefined') {
        return 0;
    }

    if (phase.type === TYPE_CRITERIA || phase.type === TYPE_COLOCATION) {
        let score = 0;
        if (typeof teamResults === 'object' && teamResults !== null) {
            for (const judgeId in teamResults) {
                const judgeScores = teamResults[judgeId];
                if (Array.isArray(judgeScores)) { // Lógica para Critérios
                    for (const s of judgeScores) {
                        score += Number(s) || 0;
                    }
                } else { // Lógica para Colocação
                    score += Number(judgeScores) || 0;
                }
            }
        }
        return score;
    } else if (phase.type === TYPE_CHECKLIST) {
        const rankedPoints = calculateRankedPoints(teams, phase, results);
        return rankedPoints[teamId] || 0;
    }

    return 0;
};

export const calculateTeamTotalScore = (teamId: number, phases: Phase[], results: ResultData, teams: Team[]): number => {
    let total = 0;

    if (results && results[teamId]) {
        for (const phase of phases) {
            total += calculatePhaseScore(teamId, phase, results, teams);
        }
    }
    return total;
};

export const getRankedTeams = (teams: Team[], phases: Phase[], results: ResultData): TeamWithScore[] => {
    const teamsWithScores = teams.map(team => ({
        ...team,
        totalScore: calculateTeamTotalScore(team.id, phases, results, teams),
    }));

    return teamsWithScores.sort((a, b) => b.totalScore - a.totalScore);
};