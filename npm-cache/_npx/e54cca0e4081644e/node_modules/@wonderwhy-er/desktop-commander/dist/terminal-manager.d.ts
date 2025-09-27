import { TerminalSession, CommandExecutionResult, ActiveSession } from './types.js';
interface CompletedSession {
    pid: number;
    output: string;
    exitCode: number | null;
    startTime: Date;
    endTime: Date;
}
export declare class TerminalManager {
    private sessions;
    private completedSessions;
    /**
     * Send input to a running process
     * @param pid Process ID
     * @param input Text to send to the process
     * @returns Whether input was successfully sent
     */
    sendInputToProcess(pid: number, input: string): boolean;
    executeCommand(command: string, timeoutMs?: number, shell?: string): Promise<CommandExecutionResult>;
    getNewOutput(pid: number): string | null;
    /**
   * Get a session by PID
   * @param pid Process ID
   * @returns The session or undefined if not found
   */
    getSession(pid: number): TerminalSession | undefined;
    forceTerminate(pid: number): boolean;
    listActiveSessions(): ActiveSession[];
    listCompletedSessions(): CompletedSession[];
}
export declare const terminalManager: TerminalManager;
export {};
