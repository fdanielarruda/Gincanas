import { Phase, Team, ResultData } from '@/types';
import {
    TYPE_CRITERIA,
    TYPE_COLOCATION,
    TYPE_CHECKLIST,
    USER_TYPE_ADMIN,
    USER_TYPE_JUDGE
} from '@/Enums/Constants';

const isValueEmpty = (val: any) => val === null || val === undefined || val === '';

export function validateForm(
    teams: Team[],
    currentPhase: Phase,
    formResults: ResultData,
    userType: number,
    judgeId: number
): boolean {
    const teamsWithResults = teams.filter(team => formResults?.[team.id]);

    if (teamsWithResults.length === 0) {
        return true;
    }

    if (currentPhase.type === TYPE_CRITERIA) {
        if (userType === USER_TYPE_ADMIN) {
            return teamsWithResults.some(team => {
                const teamResults = formResults[team.id]?.[currentPhase.id];
                if (!teamResults || Object.keys(teamResults).length === 0) return true;
                return Object.values(teamResults).some(judgeResults => {
                    return !judgeResults || (judgeResults as (number | null)[]).some(isValueEmpty);
                });
            });
        } else {
            return teamsWithResults.some(team => {
                const teamResults = formResults[team.id]?.[currentPhase.id];
                const judgeResults = teamResults?.[judgeId];
                return !judgeResults || (judgeResults as (number | null)[]).some(isValueEmpty);
            });
        }
    } else if (currentPhase.type === TYPE_CHECKLIST) {
        return teamsWithResults.some(team => {
            const teamResults = formResults[team.id]?.[currentPhase.id];
            return !teamResults || Object.values(teamResults).some(isValueEmpty);
        });
    } else if (currentPhase.type === TYPE_COLOCATION) {
        return teamsWithResults.some(team => {
            const teamResult = formResults[team.id]?.[currentPhase.id];
            return isValueEmpty(teamResult);
        });
    }

    return false;
}