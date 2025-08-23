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

        if (teamResults && teamResults[phase.id]) {
            const phaseResult = teamResults[phase.id];

            if (phase.type === TYPE_CHECKLIST && phase.checklist_colocations) {
                if (typeof phaseResult === 'object' && phaseResult !== null) {
                    for (const colocationPlace in phaseResult) {
                        const colocation = phase.checklist_colocations.find(c => c.place === colocationPlace);
                        if (colocation) {
                            const quantity = Number(phaseResult[colocationPlace]) || 0;
                            phaseScore += quantity * colocation.points;
                        }
                    }
                }
            } else {
                phaseScore = Number(phaseResult) || 0;
            }
        }
        return { ...team, phaseScore };
    });

    const sortedTeams = teamsWithPhaseScore.sort((a, b) => b.phaseScore - a.phaseScore);

    sortedTeams.forEach((team, index) => {
        const championshipPoints = phase.colocations?.[index]?.points || 0;
        scores[team.id] = championshipPoints;
    });

    return scores;
};

export const calculatePhaseScore = (teamId: number, phase: Phase, results: ResultData, teams: Team[]): number => {
    if (phase.type === TYPE_CRITERIA) {
        let score = 0;
        const teamResults = results[teamId]?.[phase.id];
        if (typeof teamResults === 'object' && teamResults !== null) {
            for (const judgeId in teamResults) {
                const judgeScores = teamResults[judgeId];
                if (Array.isArray(judgeScores)) {
                    for (const s of judgeScores) {
                        score += Number(s) || 0;
                    }
                }
            }
        }
        return score;
    } else if (phase.type === TYPE_CHECKLIST || phase.type === TYPE_COLOCATION) {
        const rankedPoints = calculateRankedPoints(teams, phase, results);
        return rankedPoints[teamId] || 0;
    }

    return 0;
};

export const calculateTeamTotalScore = (teamId: number, phases: Phase[], results: ResultData, teams: Team[]): number => {
    let total = 0;
    const teamResults = results[teamId];

    if (teamResults) {
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