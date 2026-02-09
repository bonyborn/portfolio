 // Election Data with Automatic ID Generation
        const electionData = {
            positions: [
                {
                    id: "president",
                    title: "School President",
                    candidates: [
                        {
                            id: "alex-johnson",
                            name: "Alex Johnson",
                            photo: "https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                            agenda: "Mental health support, sustainability, transparent budgeting"
                        },
                        {
                            id: "samuel-brown",
                            name: "Samuel Brown",
                            photo: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                            agenda: "School spirit, sports funding, cafeteria improvements"
                        },
                        {
                            id: "jessica-williams",
                            name: "Jessica Williams",
                            photo: "https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=749&q=80",
                            agenda: "Technology upgrades, club funding, student discounts"
                        }
                    ]
                },
                {
                    id: "secretary",
                    title: "Secretary General",
                    candidates: [
                        {
                            id: "maria-rodriguez",
                            name: "Maria Rodriguez",
                            photo: "https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=761&q=80",
                            agenda: "Transparent communication, digital archives, mentorship"
                        },
                        {
                            id: "ryan-miller",
                            name: "Ryan Miller",
                            photo: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                            agenda: "Efficient meetings, better minutes, app development"
                        }
                    ]
                },
                {
                    id: "education",
                    title: "Cabinet Secretary for Education",
                    candidates: [
                        {
                            id: "david-chen",
                            name: "David Chen",
                            photo: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                            agenda: "Peer tutoring, digital resources, study skills workshops"
                        },
                        {
                            id: "sophia-kim",
                            name: "Sophia Kim",
                            photo: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=764&q=80",
                            agenda: "Curriculum diversity, exam support, teacher feedback"
                        }
                    ]
                }
            ],
            votes: {},
            isVotingActive: false,
            voters: {}, // Format: { "VOTER-001": { department: "Computer Science", votedAt: "2023-10-15T10:30:00" } }
            departmentVotes: {}, // Track votes per department
            adminCredentials: {
                username: "election2026",
                password: "admin123"
            },
            // Auto-ID Generation System
            nextVoterID: 1,
            currentSessionID: null
        };

        // Initialize voting data
        function initializeVotingData() {
            // Check if votes exist in localStorage
            const savedVotes = localStorage.getItem('studentCouncilVotes');
            if (savedVotes) {
                electionData.votes = JSON.parse(savedVotes);
            } else {
                // Initialize votes
                electionData.positions.forEach(position => {
                    electionData.votes[position.id] = {};
                    position.candidates.forEach(candidate => {
                        electionData.votes[position.id][candidate.id] = 0;
                    });
                });
                saveVotesToStorage();
            }
            
            // Check if voters exist in localStorage
            const savedVoters = localStorage.getItem('studentCouncilVoters');
            if (savedVoters) {
                electionData.voters = JSON.parse(savedVoters);
            }
            
            // Check if department votes exist
            const savedDeptVotes = localStorage.getItem('studentCouncilDeptVotes');
            if (savedDeptVotes) {
                electionData.departmentVotes = JSON.parse(savedDeptVotes);
            } else {
                // Initialize department votes
                electionData.departmentVotes = {};
            }
            
            // Check voting status
            const votingStatus = localStorage.getItem('studentCouncilVotingStatus');
            electionData.isVotingActive = votingStatus === 'active';
            
            // Get the next voter ID from stored data
            const nextID = localStorage.getItem('studentCouncilNextVoterID');
            if (nextID) {
                electionData.nextVoterID = parseInt(nextID);
            } else {
                electionData.nextVoterID = 1;
                localStorage.setItem('studentCouncilNextVoterID', '1');
            }
            
            updateVotingUI();
            renderVotingCandidates();
            renderAllCandidatesPage();
            updateStatistics();
            updateNextVoterIDDisplay();
        }

        // Save votes to localStorage
        function saveVotesToStorage() {
            localStorage.setItem('studentCouncilVotes', JSON.stringify(electionData.votes));
            localStorage.setItem('studentCouncilVoters', JSON.stringify(electionData.voters));
            localStorage.setItem('studentCouncilDeptVotes', JSON.stringify(electionData.departmentVotes));
            localStorage.setItem('studentCouncilNextVoterID', electionData.nextVoterID.toString());
        }

        // Save voting status
        function saveVotingStatus() {
            localStorage.setItem('studentCouncilVotingStatus', electionData.isVotingActive ? 'active' : 'inactive');
        }

        // Update voting UI based on status
        function updateVotingUI() {
            const statusIndicator = document.getElementById('status-indicator');
            const statusText = document.getElementById('voting-status-text');
            const voteContainer = document.getElementById('vote-container');
            const closedMessage = document.getElementById('closed-voting-message');
            
            if (electionData.isVotingActive) {
                statusIndicator.className = 'status-indicator active';
                statusText.textContent = 'ACTIVE';
                statusText.style.color = '#27ae60';
                voteContainer.classList.add('active');
                closedMessage.style.display = 'none';
            } else {
                statusIndicator.className = 'status-indicator';
                statusText.textContent = 'CLOSED';
                statusText.style.color = '#e74c3c';
                voteContainer.classList.remove('active');
                closedMessage.style.display = 'block';
            }
        }

        // Update the next voter ID display
        function updateNextVoterIDDisplay() {
            const nextVoterIDDisplay = document.getElementById('next-voter-id-display');
            if (nextVoterIDDisplay) {
                nextVoterIDDisplay.textContent = `VOTER-${electionData.nextVoterID.toString().padStart(3, '0')}`;
            }
        }

        // Generate a new voter ID
        function generateVoterID() {
            const voterID = `VOTER-${electionData.nextVoterID.toString().padStart(3, '0')}`;
            electionData.currentSessionID = voterID;
            return voterID;
        }

        // Update the authentication modal with current voter ID
        function updateAuthModal() {
            const voterID = generateVoterID();
            document.getElementById('auto-voter-id').textContent = voterID;
            updateNextVoterIDDisplay();
        }

        // Generate a new ID (for when user wants a fresh ID)
        function generateNewID() {
            // Don't increment the main counter, just use next available
            updateAuthModal();
        }

        // Render voting candidates
        function renderVotingCandidates() {
            electionData.positions.forEach(position => {
                const container = document.getElementById(`${position.id}-candidates`);
                if (!container) return;
                
                container.innerHTML = '';
                
                position.candidates.forEach(candidate => {
                    const candidateElement = document.createElement('div');
                    candidateElement.className = 'candidate-option';
                    candidateElement.innerHTML = `
                        <input type="radio" name="${position.id}" value="${candidate.id}" id="${candidate.id}">
                        <div class="candidate-vote-info">
                            <img src="${candidate.photo}" alt="${candidate.name}" class="candidate-vote-photo">
                            <div>
                                <div class="candidate-vote-name">${candidate.name}</div>
                                <div class="candidate-vote-agenda">${candidate.agenda}</div>
                            </div>
                        </div>
                    `;
                    
                    // Add click event to select candidate
                    candidateElement.addEventListener('click', function() {
                        const radioInput = this.querySelector('input[type="radio"]');
                        radioInput.checked = true;
                        
                        // Remove selected class from all candidates in this position
                        const allCandidates = container.querySelectorAll('.candidate-option');
                        allCandidates.forEach(c => c.classList.remove('selected'));
                        
                        // Add selected class to clicked candidate
                        this.classList.add('selected');
                        
                        validateVote();
                    });
                    
                    container.appendChild(candidateElement);
                });
            });
        }

        // Render all candidates page
        function renderAllCandidatesPage() {
            const container = document.getElementById('all-candidates-container');
            if (!container) return;
            
            container.innerHTML = '';
            
            electionData.positions.forEach(position => {
                position.candidates.forEach(candidate => {
                    const candidateCard = document.createElement('div');
                    candidateCard.className = 'candidate-card';
                    candidateCard.innerHTML = `
                        <img src="${candidate.photo}" alt="${candidate.name}" class="card-photo">
                        <div class="card-info">
                            <h3>${candidate.name}</h3>
                            <p class="candidate-position">${position.title}</p>
                            <p>${candidate.agenda}</p>
                            <button class="rating-btn" onclick="openRatingModal('${candidate.name}', '${position.title}')">Rate This Candidate</button>
                        </div>
                    `;
                    container.appendChild(candidateCard);
                });
            });
        }

        // Validate vote (check if all positions have a selection)
        function validateVote() {
            let allSelected = true;
            const validationMsg = document.getElementById('vote-validation');
            const submitBtn = document.getElementById('submit-vote-btn');
            
            electionData.positions.forEach(position => {
                const selected = document.querySelector(`input[name="${position.id}"]:checked`);
                if (!selected) {
                    allSelected = false;
                }
            });
            
            if (allSelected) {
                validationMsg.textContent = '';
                validationMsg.style.color = '#27ae60';
                submitBtn.disabled = false;
            } else {
                validationMsg.textContent = 'Please select a candidate for each position.';
                validationMsg.style.color = '#e74c3c';
                submitBtn.disabled = true;
            }
            
            return allSelected;
        }

        // Open voter authentication modal
        function authenticateVoter() {
            if (!validateVote()) {
                alert('Please select a candidate for each position before voting.');
                return;
            }
            
            if (!electionData.isVotingActive) {
                alert('Voting is currently closed. Please wait for the voting period to begin.');
                return;
            }
            
            // Generate and display voter ID
            updateAuthModal();
            document.getElementById('auth-modal').style.display = 'flex';
        }

        // Close auth modal
        function closeAuthModal() {
            document.getElementById('auth-modal').style.display = 'none';
            document.getElementById('auth-error').style.display = 'none';
            document.getElementById('department').selectedIndex = 0;
        }

        // Verify voter and record vote
        function verifyVoter() {
            const department = document.getElementById('department').value;
            const errorDiv = document.getElementById('auth-error');
            const errorText = document.getElementById('error-text');
            
            // Check if department is selected
            if (!department) {
                errorText.textContent = 'Please select your department.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Get the current voter ID
            const voterID = electionData.currentSessionID;
            
            // Check if this voter ID has already voted (shouldn't happen with auto-increment)
            if (electionData.voters[voterID]) {
                errorText.textContent = 'This Voter ID has already been used. Generating a new one...';
                errorDiv.style.display = 'block';
                generateNewID();
                return;
            }
            
            // Record vote
            recordVote(voterID, department);
            closeAuthModal();
            showVoteConfirmation(voterID);
        }

        // Record the vote
        function recordVote(voterID, department) {
            // Mark voter as voted with department info
            electionData.voters[voterID] = {
                department: department,
                votedAt: new Date().toISOString()
            };
            
            // Update department vote count
            if (!electionData.departmentVotes[department]) {
                electionData.departmentVotes[department] = 0;
            }
            electionData.departmentVotes[department] += 1;
            
            // Record votes for each position
            electionData.positions.forEach(position => {
                const selectedCandidate = document.querySelector(`input[name="${position.id}"]:checked`);
                if (selectedCandidate) {
                    electionData.votes[position.id][selectedCandidate.value] += 1;
                }
            });
            
            // Increment the next voter ID for future voters
            electionData.nextVoterID += 1;
            
            // Save to storage
            saveVotesToStorage();
            
            // Clear selections
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                radio.checked = false;
                radio.closest('.candidate-option').classList.remove('selected');
            });
            
            // Update validation
            validateVote();
            
            // Update statistics and display
            updateStatistics();
            updateNextVoterIDDisplay();
        }

        // Show vote confirmation
        function showVoteConfirmation(voterID) {
            // Build vote summary
            let summaryHTML = '';
            electionData.positions.forEach(position => {
                const selectedCandidate = document.querySelector(`input[name="${position.id}"]:checked`);
                if (selectedCandidate) {
                    const candidateId = selectedCandidate.value;
                    const candidate = position.candidates.find(c => c.id === candidateId);
                    summaryHTML += `<p><strong>${position.title}:</strong> ${candidate.name}</p>`;
                }
            });
            
            document.getElementById('vote-summary').innerHTML = summaryHTML;
            document.getElementById('confirmed-voter-id').textContent = voterID;
            document.getElementById('confirmation-modal').style.display = 'flex';
        }

        // Close confirmation modal
        function closeConfirmationModal() {
            document.getElementById('confirmation-modal').style.display = 'none';
        }

        // Update statistics dashboard
        function updateStatistics() {
            const statsContainer = document.getElementById('stats-dashboard');
            const deptContainer = document.getElementById('department-breakdown-list');
            
            if (!statsContainer || !deptContainer) return;
            
            // Calculate total votes
            let totalVotes = Object.keys(electionData.voters).length;
            
            // Calculate votes per position
            let presidentVotes = 0;
            let secretaryVotes = 0;
            let educationVotes = 0;
            
            if (electionData.votes.president) {
                presidentVotes = Object.values(electionData.votes.president).reduce((a, b) => a + b, 0);
            }
            if (electionData.votes.secretary) {
                secretaryVotes = Object.values(electionData.votes.secretary).reduce((a, b) => a + b, 0);
            }
            if (electionData.votes.education) {
                educationVotes = Object.values(electionData.votes.education).reduce((a, b) => a + b, 0);
            }
            
            // Calculate next voter ID
            const nextVoterID = `VOTER-${electionData.nextVoterID.toString().padStart(3, '0')}`;
            
            // Update stats dashboard
            statsContainer.innerHTML = `
                <div class="stat-card">
                    <div class="stat-value">${totalVotes}</div>
                    <div class="stat-label">Total Votes Cast</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${presidentVotes}</div>
                    <div class="stat-label">President Votes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${secretaryVotes}</div>
                    <div class="stat-label">Secretary Votes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${nextVoterID}</div>
                    <div class="stat-label">Next Voter ID</div>
                </div>
            `;
            
            // Update department breakdown
            let deptHTML = '';
            const departments = [
                "Computer Science", "Mathematics", "Physics", "Biology",
                "Chemistry", "Engineering", "Business", "Arts", "Other"
            ];
            
            departments.forEach(dept => {
                const count = electionData.departmentVotes[dept] || 0;
                if (count > 0) {
                    deptHTML += `
                        <div class="department-row">
                            <span class="department-name">${dept}</span>
                            <span class="department-count">${count} vote${count !== 1 ? 's' : ''}</span>
                        </div>
                    `;
                }
            });
            
            // If no departments have voted yet
            if (deptHTML === '') {
                deptHTML = '<p style="text-align: center; color: #7f8c8d;">No votes recorded yet.</p>';
            }
            
            deptContainer.innerHTML = deptHTML;
        }

        // Refresh statistics
        function refreshStats() {
            updateStatistics();
            alert('Statistics refreshed!');
        }

        // Admin functions
        function showAdminLogin() {
            document.getElementById('admin-modal').style.display = 'flex';
        }

        function closeAdminModal() {
            document.getElementById('admin-modal').style.display = 'none';
            document.getElementById('admin-error').style.display = 'none';
            document.getElementById('admin-username').value = '';
            document.getElementById('admin-password').value = '';
        }

        function verifyAdmin() {
            const username = document.getElementById('admin-username').value.trim();
            const password = document.getElementById('admin-password').value.trim();
            const errorDiv = document.getElementById('admin-error');
            
            if (username === electionData.adminCredentials.username && 
                password === electionData.adminCredentials.password) {
                // Show admin panel
                document.getElementById('admin-panel').style.display = 'block';
                closeAdminModal();
            } else {
                errorDiv.style.display = 'block';
            }
        }

        function openVoting() {
            electionData.isVotingActive = true;
            saveVotingStatus();
            updateVotingUI();
            alert('Voting is now OPEN! Voter IDs will be automatically generated.');
        }

        function pauseVoting() {
            electionData.isVotingActive = false;
            saveVotingStatus();
            updateVotingUI();
            alert('Voting has been PAUSED. No new votes can be cast.');
        }

        function closeVoting() {
            if (confirm('Are you sure you want to END the voting? This cannot be undone.')) {
                electionData.isVotingActive = false;
                saveVotingStatus();
                updateVotingUI();
                alert('Voting is now CLOSED. No more votes can be cast.');
            }
        }

        // Generate test votes for demonstration
        function generateTestVotes() {
            if (confirm('Generate 10 test votes for demonstration? This will add fake votes to the system.')) {
                const departments = ["Computing and Informatics", "Electrical Engineering", "Automative Engineering", "Callinery Arts", "Engineering", "Business", "Secretarial Studies"];
                const candidates = {
                    president: ["Boniface-Nzau", "sammy", "Ruto"],
                    secretary: ["mary", "kimani"],
                    education: ["david", "kelvin"]
                };
                
                for (let i = 0; i < 10; i++) {
                    const voterID = `TEST-${(i + 1).toString().padStart(3, '0')}`;
                    const department = departments[Math.floor(Math.random() * departments.length)];
                    
                    // Record test voter
                    electionData.voters[voterID] = {
                        department: department,
                        votedAt: new Date().toISOString(),
                        isTest: true
                    };
                    
                    // Update department count
                    electionData.departmentVotes[department] = (electionData.departmentVotes[department] || 0) + 1;
                    
                    // Record random votes
                    electionData.positions.forEach(position => {
                        const randomCandidate = candidates[position.id][Math.floor(Math.random() * candidates[position.id].length)];
                        electionData.votes[position.id][randomCandidate] = (electionData.votes[position.id][randomCandidate] || 0) + 1;
                    });
                }
                
                // Save and update
                saveVotesToStorage();
                updateStatistics();
                alert('10 test votes generated successfully!');
            }
        }

        function viewResults() {
            // Build results table
            let resultsHTML = '';
            
            electionData.positions.forEach(position => {
                resultsHTML += `<h3>${position.title}</h3>`;
                resultsHTML += `<table class="results-table">`;
                resultsHTML += `<thead><tr><th>Candidate</th><th>Votes</th><th>Percentage</th></tr></thead>`;
                resultsHTML += `<tbody>`;
                
                // Calculate total votes for this position
                let totalVotes = 0;
                position.candidates.forEach(candidate => {
                    totalVotes += electionData.votes[position.id][candidate.id] || 0;
                });
                
                // Add each candidate's results
                position.candidates.forEach(candidate => {
                    const votes = electionData.votes[position.id][candidate.id] || 0;
                    const percentage = totalVotes > 0 ? ((votes / totalVotes) * 100).toFixed(1) : 0;
                    
                    resultsHTML += `<tr>`;
                    resultsHTML += `<td>${candidate.name}</td>`;
                    resultsHTML += `<td class="vote-count">${votes}</td>`;
                    resultsHTML += `<td>${percentage}% <div class="vote-progress-bar"><div class="vote-progress" style="width: ${percentage}%"></div></div></td>`;
                    resultsHTML += `</tr>`;
                });
                
                resultsHTML += `</tbody></table>`;
                resultsHTML += `<p style="margin-top: 0.5rem; color: #7f8c8d;">Total votes for this position: ${totalVotes}</p>`;
                resultsHTML += `<hr style="margin: 1.5rem 0;">`;
            });
            
            // Add overall stats
            let totalVotesAll = Object.keys(electionData.voters).length;
            let nextVoterID = `VOTER-${electionData.nextVoterID.toString().padStart(3, '0')}`;
            
            resultsHTML += `<div style="background-color: #f8f9fa; padding: 1rem; border-radius: 8px; margin-top: 1rem;">`;
            resultsHTML += `<h4>Overall Statistics</h4>`;
            resultsHTML += `<p>Total votes cast: <strong>${totalVotesAll}</strong></p>`;
            resultsHTML += `<p>Next voter ID: <strong>${nextVoterID}</strong></p>`;
            resultsHTML += `<p>Voting status: <strong>${electionData.isVotingActive ? 'ACTIVE' : 'CLOSED'}</strong></p>`;
            
            // Add department breakdown summary
            let topDepartments = Object.entries(electionData.departmentVotes)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 3);
            
            if (topDepartments.length > 0) {
                resultsHTML += `<p>Top voting departments: `;
                topDepartments.forEach(([dept, count], index) => {
                    resultsHTML += `<strong>${dept}</strong> (${count})`;
                    if (index < topDepartments.length - 1) resultsHTML += ', ';
                });
                resultsHTML += `</p>`;
            }
            
            resultsHTML += `</div>`;
            
            document.getElementById('results-container').innerHTML = resultsHTML;
            document.getElementById('results-modal').style.display = 'flex';
        }

        function closeResultsModal() {
            document.getElementById('results-modal').style.display = 'none';
        }

        function exportResults() {
            // Create a downloadable file with results
            let resultsText = "STUDENT COUNCIL ELECTION RESULTS\n";
            resultsText += "===============================\n\n";
            resultsText += `Generated on: ${new Date().toLocaleString()}\n`;
            resultsText += `Total voters: ${Object.keys(electionData.voters).length}\n`;
            resultsText += `Next voter ID: VOTER-${electionData.nextVoterID.toString().padStart(3, '0')}\n\n`;
            
            electionData.positions.forEach(position => {
                resultsText += `${position.title}\n`;
                resultsText += "--------------------------------\n";
                
                // Calculate total votes for this position
                let totalVotes = 0;
                position.candidates.forEach(candidate => {
                    totalVotes += electionData.votes[position.id][candidate.id] || 0;
                });
                
                position.candidates.forEach(candidate => {
                    const votes = electionData.votes[position.id][candidate.id] || 0;
                    const percentage = totalVotes > 0 ? ((votes / totalVotes) * 100).toFixed(1) : 0;
                    
                    resultsText += `${candidate.name}: ${votes} votes (${percentage}%)\n`;
                });
                
                resultsText += `Total: ${totalVotes} votes\n\n`;
            });
            
            // Add department breakdown
            resultsText += `DEPARTMENT VOTING BREAKDOWN\n`;
            resultsText += "--------------------------------\n";
            Object.entries(electionData.departmentVotes)
                .sort((a, b) => b[1] - a[1])
                .forEach(([dept, count]) => {
                    resultsText += `${dept}: ${count} vote${count !== 1 ? 's' : ''}\n`;
                });
            
            // Create blob and download link
            const blob = new Blob([resultsText], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `election_results_${new Date().toISOString().split('T')[0]}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            alert('Results exported successfully!');
        }

        function resetVotes() {
            if (confirm('WARNING: This will reset ALL votes, voter records, and the ID counter. Are you sure?')) {
                // Reset votes
                electionData.positions.forEach(position => {
                    electionData.votes[position.id] = {};
                    position.candidates.forEach(candidate => {
                        electionData.votes[position.id][candidate.id] = 0;
                    });
                });
                
                // Reset voters
                electionData.voters = {};
                
                // Reset department votes
                electionData.departmentVotes = {};
                
                // Reset ID counter
                electionData.nextVoterID = 1;
                electionData.currentSessionID = null;
                
                // Save to storage
                saveVotesToStorage();
                
                // Update statistics
                updateStatistics();
                updateNextVoterIDDisplay();
                
                alert('All votes, voter records, and ID counter have been reset.');
                viewResults(); // Refresh results view
            }
        }

        // Navigation function
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show the selected section
            document.getElementById(`${sectionId}-section`).style.display = 'block';
            
            // Update active nav link
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('active');
            });
            
            // Find and activate the clicked nav link
            document.querySelectorAll('nav a').forEach(link => {
                if (link.textContent.includes(sectionId.replace('-', ' ')) || 
                   (sectionId === 'president' && link.textContent.includes('President')) ||
                   (sectionId === 'voting' && link.textContent.includes('Voting')) ||
                   (sectionId === 'feedback' && link.textContent.includes('Voice')) ||
                   (sectionId === 'stats' && link.textContent.includes('Statistics'))) {
                    link.classList.add('active');
                }
            });
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            // Update statistics if viewing stats page
            if (sectionId === 'stats') {
                updateStatistics();
            }
        }

        // Countdown timer for voting
        function startCountdown() {
            // Set election date (October 15, 2023)
            const electionDate = new Date('October 15, 2023 08:00:00').getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = electionDate - now;
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Display the countdown
                const countdownElement = document.getElementById('countdown');
                if (countdownElement) {
                    if (distance < 0) {
                        countdownElement.innerHTML = "Voting is now open!";
                    } else {
                        countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                    }
                }
                
                // Update election date display
                const electionTimer = document.getElementById('election-timer');
                if (electionTimer) {
                    if (distance < 0) {
                        electionTimer.innerHTML = "Election: Voting Now Open!";
                        electionTimer.style.backgroundColor = "#27ae60";
                    } else if (distance < 86400000) { // Less than 24 hours
                        electionTimer.innerHTML = "Election: Voting Opens Tomorrow!";
                        electionTimer.style.backgroundColor = "#f39c12";
                    }
                }
            }
            
            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Feedback submission
        function submitFeedback() {
            const department = document.getElementById('feedback-dept').value;
            const feedback = document.getElementById('feedback').value;
            
            if (!department) {
                alert('Please select a department.');
                return;
            }
            
            if (!feedback.trim()) {
                alert('Please enter your feedback.');
                return;
            }
            
            // Show confirmation message
            document.getElementById('confirmation-message').style.display = 'block';
            
            // Clear form
            document.getElementById('feedback-dept').value = '';
            document.getElementById('feedback').value = '';
            
            // Scroll to confirmation
            document.getElementById('confirmation-message').scrollIntoView({ behavior: 'smooth' });
            
            // Hide confirmation after 5 seconds
            setTimeout(() => {
                document.getElementById('confirmation-message').style.display = 'none';
            }, 5000);
        }

        // Rating Modal functionality
        function openRatingModal(name, position) {
            alert(`Rating feature for ${name} (${position}) would open here.`);
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            showSection('president');
            initializeVotingData();
        });
