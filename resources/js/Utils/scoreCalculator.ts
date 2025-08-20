import { Phase, ResultData, Team, Judge, Colocation } from '@/types';

interface TeamWithScore extends Team {
    totalScore: number;
}

const TYPE_CRITERIA = 1;
const TYPE_CHECKLIST = 4;

export const calculatePhaseScore = (teamId: number, phase: Phase, results: ResultData): number => {
    let score = 0;
    const teamResults = results[teamId];

    if (!teamResults || !teamResults[phase.id]) {
        return 0;
    }

    const phaseResult = teamResults[phase.id];

    if (phase.type === TYPE_CRITERIA) {
        if (typeof phaseResult === 'object' && phaseResult !== null) {
            for (const judgeId in phaseResult) {
                const judgeScores = phaseResult[judgeId];
                if (Array.isArray(judgeScores)) {
                    for (const s of judgeScores) {
                        score += Number(s) || 0;
                    }
                }
            }
        }
    } else if (phase.type === TYPE_CHECKLIST && phase.colocations) {
        if (typeof phaseResult === 'object' && phaseResult !== null) {
            for (const colocationPlace in phaseResult) {
                const colocation = phase.colocations.find(c => c.place === colocationPlace);
                if (colocation) {
                    const quantity = Number(phaseResult[colocationPlace]) || 0;
                    score += quantity * colocation.points;
                }
            }
        }
    } else {
        score += Number(phaseResult) || 0;
    }

    return score;
};

export const calculateTeamTotalScore = (teamId: number, phases: Phase[], results: ResultData): number => {
    let total = 0;
    const teamResults = results[teamId];

    if (teamResults) {
        for (const phase of phases) {
            total += calculatePhaseScore(teamId, phase, results);
        }
    }
    return total;
};

export const getRankedTeams = (teams: Team[], phases: Phase[], results: ResultData): TeamWithScore[] => {
    const teamsWithScores = teams.map(team => ({
        ...team,
        totalScore: calculateTeamTotalScore(team.id, phases, results),
    }));

    return teamsWithScores.sort((a, b) => b.totalScore - a.totalScore);
};